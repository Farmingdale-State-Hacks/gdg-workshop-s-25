# DetectiveCase – Crime Pattern Explorer Using Google Cloud

![DetectiveCase Logo](https://images.unsplash.com/photo-1536599424071-0b215a388ba7?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200&q=80)

## 💡 What It Does

DetectiveCase is a crime data web app that helps people explore where and when different crimes happen in New York City. It shows patterns in thefts, burglaries, and other crimes using maps and graphs.

It's built to help criminal justice students, researchers, and curious citizens understand how crime changes over time — especially during big events like COVID-19 lockdowns.

## ☁️ How We Used Google Cloud

We used several GCP services to make the app work smoothly and handle big crime datasets:

**🌐 Cloud Run**
We deployed the backend on Cloud Run. It runs our Python Flask app and scales up/down automatically depending on how many people use it — no server setup needed.

**📊 BigQuery**
This is where all the crime data lives. We used over 6 million records from NYC Open Data (like NYPD Complaint Data). BigQuery lets us run fast searches across huge files — way better than trying to use a local database.

**🪣 Cloud Storage**
We store static stuff here — like map files, exported charts, and uploaded CSVs. Cloud Storage makes it easy to host and share them reliably.

## 🧩 Technology Stack

This project uses a multi-language architecture with each component handling a specific role:

- **Frontend**: Java with Spring Boot
- **Backend**: PHP
- **Server**: Python with Flask
- **ML Component**: Node.js with TensorFlow.js

## 📦 Project Structure

```
detectivecase/
├── frontend/                # Java Spring Boot frontend
│   ├── src/main/java        # Java source code
│   ├── src/main/resources   # Templates and static resources
│   └── pom.xml              # Maven dependencies
├── backend/                 # PHP backend API
│   ├── public/              # Public-facing files
│   └── src/                 # PHP source code
├── server/                  # Python server for GCP integration
│   ├── app/                 # Flask application
│   └── requirements.txt     # Python dependencies
├── ml/                      # Node.js machine learning component
│   ├── src/                 # JS source code
│   └── package.json         # NPM dependencies
└── README.md                # This file
```

## 📊 Data Sources We Used

- **NYPD Complaint Data** (Historic and Current)
  From NYC Open Data: [https://data.cityofnewyork.us](https://data.cityofnewyork.us)

- **NYC Zoning & Commercial Zones**
  For filtering crime locations by business areas (from the NYC Department of City Planning)

- **US Census Data**
  To compare crime to population density (used for extra context)

## 🚀 Getting Started

### Prerequisites

- Java 11 or higher
- PHP 7.4 or higher
- Python 3.8 or higher
- Node.js 16 or higher
- Google Cloud account with BigQuery, Cloud Storage, and Cloud Run access

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/detectivecase.git
   cd detectivecase
   ```

2. Install dependencies:

   ```bash
   # Install all dependencies
   bun install
   ```

3. Configure Google Cloud credentials:

   ```bash
   # Set up credentials - get these from your Google Cloud Console
   export GOOGLE_APPLICATION_CREDENTIALS="/path/to/your/credentials.json"
   ```

4. Run the application:

   ```bash
   # Start all components
   bun run dev
   ```

5. Visit `http://localhost:8000` to view the application.

## 🗺️ Features

- Heatmaps of theft incidents across NYC before, during, and after COVID-19
- Charts showing which neighborhoods had rising or falling crime
- Filters for crime type, time range, and location
- Machine learning-based crime trend prediction

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgements

- Data provided by NYC Open Data
- Google Cloud for hosting and data processing
- All contributors who helped build this project
