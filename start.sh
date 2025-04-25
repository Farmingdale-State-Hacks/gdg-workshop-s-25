#!/bin/bash

# DetectiveCase - Crime Pattern Explorer
# Startup script to launch all components

echo "🔍 Starting DetectiveCase Application..."
echo "----------------------------------------"

# Create a directory for logs if it doesn't exist
mkdir -p logs

# Function to check if a command exists
command_exists() {
  command -v "$1" >/dev/null 2>&1
}

# Check for required dependencies
echo "✅ Checking dependencies..."
MISSING_DEPS=0

if ! command_exists java; then
  echo "❌ Java not found. Please install Java 11 or higher."
  MISSING_DEPS=1
fi

if ! command_exists php; then
  echo "❌ PHP not found. Please install PHP 7.4 or higher."
  MISSING_DEPS=1
fi

if ! command_exists python; then
  echo "❌ Python not found. Please install Python 3.8 or higher."
  MISSING_DEPS=1
fi

if ! command_exists node; then
  echo "❌ Node.js not found. Please install Node.js 16 or higher."
  MISSING_DEPS=1
fi

if [ $MISSING_DEPS -eq 1 ]; then
  echo "❌ Missing dependencies. Please install them and try again."
  exit 1
fi

echo "✅ All dependencies found."

# Function to start a service in the background
start_service() {
  local name=$1
  local command=$2
  local logfile=$3
  
  echo "🚀 Starting $name..."
  cd $4 && $command > "../logs/$logfile" 2>&1 &
  local pid=$!
  echo "🔖 $name started with PID $pid (logs: logs/$logfile)"
  echo $pid > "../logs/$name.pid"
  cd ..
}

# Check if Google Cloud credentials are set
if [ -z "$GOOGLE_APPLICATION_CREDENTIALS" ]; then
  echo "⚠️ Warning: GOOGLE_APPLICATION_CREDENTIALS environment variable not set."
  echo "⚠️ Some features may not work correctly."
fi

# Start Python Flask server
echo "----------------------------------------"
start_service "python-server" "make run" "python-server.log" "server"
echo "----------------------------------------"

# Wait a moment for the Python server to initialize
sleep 2

# Start PHP backend
echo "----------------------------------------"
if command_exists composer; then
  echo "✅ Installing PHP dependencies..."
  (cd backend && composer install --no-interaction)
else
  echo "⚠️ Warning: Composer not found. PHP dependencies may not be installed correctly."
fi

start_service "php-backend" "php -S localhost:8001 -t public" "php-backend.log" "backend"
echo "----------------------------------------"

# Start Node.js ML component
echo "----------------------------------------"
if command_exists npm; then
  echo "✅ Installing Node.js dependencies..."
  (cd ml && npm install --no-audit)
else
  echo "⚠️ Warning: npm not found. Node.js dependencies may not be installed correctly."
fi

start_service "ml-component" "node src/index.js" "ml-component.log" "ml"
echo "----------------------------------------"

# Start Java Spring Boot frontend
echo "----------------------------------------"
if command_exists mvn; then
  echo "✅ Building Java frontend..."
  (cd frontend && mvn package -DskipTests)
  start_service "java-frontend" "java -jar target/detective-case-frontend-0.0.1-SNAPSHOT.jar" "java-frontend.log" "frontend"
else
  echo "⚠️ Warning: Maven not found. Trying to run with existing build..."
  start_service "java-frontend" "./mvnw spring-boot:run" "java-frontend.log" "frontend"
fi
echo "----------------------------------------"

echo "🎉 All services started! The application should be available at:"
echo "🌐 http://localhost:8000"
echo ""
echo "📝 Logs are available in the logs directory"
echo "✋ To stop all services, run: ./stop.sh"

# Create a stop script
cat > stop.sh << 'EOF'
#!/bin/bash

echo "🛑 Stopping DetectiveCase services..."

# Function to stop a service
stop_service() {
  local name=$1
  if [ -f "logs/$name.pid" ]; then
    local pid=$(cat "logs/$name.pid")
    echo "🛑 Stopping $name (PID: $pid)..."
    kill $pid 2>/dev/null || kill -9 $pid 2>/dev/null
    rm "logs/$name.pid"
    echo "✅ $name stopped"
  else
    echo "⚠️ PID file for $name not found"
  fi
}

# Stop all services
stop_service "java-frontend"
stop_service "php-backend"
stop_service "python-server"
stop_service "ml-component"

echo "✅ All services stopped"
EOF

chmod +x stop.sh

echo "Created stop.sh script to stop all services" 