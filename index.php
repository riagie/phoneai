<?php

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Dotenv\Dotenv;
use GuzzleHttp\Client;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->load();
$httpClient = new Client(['timeout' => 10, 'verify' => false]);
$datasheet  = __DIR__ . '/datasheet.json';

$worker = new Worker('http://' . $_ENV['HOST'] . ':' . $_ENV['PORT']);

$worker->onMessage = function (TcpConnection $connection, Request $req) use ($httpClient, $datasheet) {
	$path = trim($req->path(), '/');

	if ($path === 'test') {
		return send($connection, ['message' => 'the test endpoint is working!']);
	}

	if (preg_match('/^\+?\d[\d\-\s]{9,}$/', $path)) {
		return handlePhoneNumber($connection, $httpClient, $datasheet, $path);
	}

	send($connection, ['message' => 'invalid request parameter'], 404);
};

function handlePhoneNumber($connection, $httpClient, $datasheet, $number)
{
	$number = formatPhoneNumber($number);

	if ($contact = datasheet($datasheet, $number)) {
		$months = (int) ($_ENV['DATA_EXPIRY_MONTHS'] ?? 1);
		if (($contact['timestamp'] ?? 0) >= strtotime("-{$months} months")) {
			return send($connection, $contact);
		}
	}

	$contact = fetchWithApi($httpClient, $_ENV['API_URL'], ['number' => $number]);
	if (!is_array($contact)) {
		return send($connection, ['messages' => 'bad gateway'], 502);
	}

	if (!empty($contact['tag']) && count($contact['tag']) > 0) {
		$openAiHeaders = ['Authorization' => 'Bearer ' . ($_ENV['OPENAI_KEY'] ?? '')];

		$contact['timestamp']   = time();
		$contact['description'] = fetchWithApi($httpClient, $_ENV['OPENAI_URL'], [
			'model'    => 'gpt-4o-mini',
			'messages' => [
				['role' => 'system', 'content' => 'Berdasarkan daftar nama kontak ini, buat deskripsi tentang siapa orang tersebut, dimana dia kuliah, dimana dia bekerja, apa hobinya, apa komunitas yang diikuti, apa kendaraan yang dimiliki atau diminati, siapa relasi keluarganya, siapa relasi pertemanannya, pendidikan, karier, kendaraan, serta semua informasi lainnya yang bisa diperoleh berdasarkan data kontak tersebut.'],
				['role' => 'user', 'content' => implode(', ', (array) $contact['tag'])],
			],
			'stream' => false,
		], $openAiHeaders)['choices'][0]['message']['content'] ?? 'No description available';

		$contact['tag'] = implode(' # ', (array) $contact['tag']);
	}

	$contact = array_merge(['timestamp' => $contact['timestamp']], $contact);

	saveToDatasheet($datasheet, $contact);

	send($connection, $contact);
}

function formatPhoneNumber($num)
{
	$num = preg_replace('/[^\d+]/', '', str_replace(' ', '', $num));
	$num = preg_replace(['/^\+?00/', '/^\+?0/', '/^\+?([1-9])/'], ['+', '+62', '+$1'], $num);

	return (substr($num, 0, 1) === '+') ? preg_replace('/[^\d]/', '', $num) : null;
}

function datasheet($datasheet, $number)
{
	if (!file_exists($datasheet) || filesize($datasheet) === 0) {
		return null;
	}

	$data  = json_decode(file_get_contents($datasheet), true);
	$index = array_search($number, array_column($data, 'nomor'));

	return $index !== false ? $data[$index] : null;
}

function saveToDatasheet($datasheet, $contact)
{
	$data = file_exists($datasheet) ? json_decode(file_get_contents($datasheet), true) : [];

	if (!is_array($data)) {
		$data = [];
	}

	$index = array_search($contact['nomor'], array_column($data, 'nomor'));

	if ($index !== false) {
		$data[$index] = $contact;
	} else {
		$data[] = $contact;
	}

	file_put_contents($datasheet, json_encode($data, JSON_PRETTY_PRINT));
}

function fetchWithApi($client, $url, $data, $headers = [])
{
	try {
		$res = $client->postAsync($url, [
			'json'    => $data,
			'headers' => array_merge(['Content-Type' => 'application/json'], $headers),
		])->wait();

		return json_decode((string) $res->getBody(), true);
	} catch (\Exception $e) {
		return null;
	}
}

function send($connection, $data, $status = 200)
{
	$connection->send(new Workerman\Protocols\Http\Response($status, ['Content-Type' => 'application/json'], json_encode($data, JSON_PRETTY_PRINT)));
}

Worker::runAll();
