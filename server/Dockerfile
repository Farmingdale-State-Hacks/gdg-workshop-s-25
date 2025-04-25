FROM python:3.9-slim

WORKDIR /app

# Copy requirements file and install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy source code
COPY . .

# Set environment variables
ENV PORT=8002
ENV PYTHONUNBUFFERED=1

# Expose the port the app runs on
EXPOSE 8002

# Command to run the application
CMD ["python", "app.py"] 