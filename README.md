# **📞 PhoneAI - Intelligent Contact Lookup**  
PhoneAI is a **Workerman-based** system that enables contact searches using phone numbers. It also integrates with **OpenAI** to generate automatic descriptions based on contact data.  

## **🔹 Key Features:**  
✅ **Contact Lookup** → Retrieve information based on phone numbers.  
✅ **Automatic Data Caching** → Stores data in `datasheet.json` and updates it automatically.  
✅ **Data Validation** → If the data is older than the configured limit (`DATA_EXPIRY_MONTHS` in `.env`), it will be re-fetched from the API.  
✅ **AI-Generated Descriptions** → Uses OpenAI to generate additional details about contacts.  
✅ **Environment Configuration** → Supports `.env` for easy setup and configuration.  
✅ **API Endpoints:**  
   - `GET /{phoneNumber}` → Lookup contact details by phone number.  
   - `GET /test` → Check if the server is running properly.  

## **🛠 Technologies Used:**  
🔹 PHP + Workerman (HTTP Server)  
🔹 GuzzleHttp (API Requests)  
🔹 OpenAI API (AI Descriptions)  
🔹 Dotenv (Configuration)  

## **🚀 Installation & Setup:**  

### **1️⃣ Clone the Repository**  
```sh
git clone https://github.com/username/phoneai.git
cd phoneai
```

### **2️⃣ Install Dependencies**  
```sh
composer install
```

### **3️⃣ Create and Configure `.env` File**  
```sh
cp .env.example .env
```
Update the `.env` file with your API keys and server configuration.

### **4️⃣ Run the Server**  
```sh
php index.php start -d
```

### **5️⃣ Open in Browser**  
[http://localhost:8080](http://localhost:8080)  

## **📌 Available Commands:**  
```sh
php index.php start        # Start the server  
php index.php start -d     # Start in daemon mode  
php index.php status       # Check the server status  
php index.php status -d    # Check detailed status  
php index.php connections  # View active connections  
php index.php stop         # Stop the server  
php index.php stop -g      # Stop all processes  
php index.php restart      # Restart the server  
php index.php reload       # Reload workers  
php index.php reload -g    # Reload all workers  
```

---

📌 **Notes:**  
- Make sure your `.env` file is properly configured.  
- To modify the cache expiration period, update `DATA_EXPIRY_MONTHS` in `.env`.  
- Workerman should be started using `php index.php start -d` for daemon mode.  

🛠 **Developed by [Ogienurdiana](https://github.com/username)** 🚀  

---
