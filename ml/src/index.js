import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import * as tf from '@tensorflow/tfjs-node';
import { linearRegression, linearRegressionLine } from 'simple-statistics';
import axios from 'axios';

// Load environment variables
dotenv.config();

const app = express();
const PORT = process.env.PORT || 8003;

// Middleware
app.use(cors());
app.use(express.json());

// Base URL for PHP API
const API_BASE_URL = process.env.API_BASE_URL || 'http://localhost:8001/api';

// Global variables to store models
let crimeModel = null;

// Initialize the application
async function init() {
  console.log('Initializing ML service...');
  try {
    // Load or create the model
    crimeModel = await loadOrCreateModel();
    console.log('Model loaded successfully');
  } catch (error) {
    console.error('Error initializing models:', error);
  }
}

// Load a saved model or create a new one
async function loadOrCreateModel() {
  try {
    // Try to load existing model
    const model = await tf.loadLayersModel('file://./models/crime_prediction/model.json');
    console.log('Loaded existing model');
    return model;
  } catch (error) {
    console.log('Creating new model...');

    // Create a simple sequential model
    const model = tf.sequential();

    // Add layers
    model.add(tf.layers.dense({
      inputShape: [3], // [month, day, hour]
      units: 32,
      activation: 'relu'
    }));

    model.add(tf.layers.dense({
      units: 16,
      activation: 'relu'
    }));

    model.add(tf.layers.dense({
      units: 1 // Output is crime count
    }));

    // Compile the model
    model.compile({
      optimizer: tf.train.adam(),
      loss: 'meanSquaredError'
    });

    console.log('New model created');
    return model;
  }
}

// Function to fetch crime data for training
async function fetchCrimeData() {
  try {
    const response = await axios.get(`${API_BASE_URL}/crimes?limit=5000`);
    return response.data.data || [];
  } catch (error) {
    console.error('Error fetching crime data:', error);
    return [];
  }
}

// API Endpoints
app.get('/api/health', (req, res) => {
  res.json({
    status: 'healthy',
    message: 'ML service is running',
    modelLoaded: crimeModel !== null
  });
});

// Endpoint to predict crime trends
app.get('/api/predict/trend', async (req, res) => {
  try {
    // Get crime data for a specific type and time range
    const { type = 'THEFT', period = 'month' } = req.query;

    // Fetch crime data
    const crimes = await fetchCrimeData();

    // Filter by type if specified
    const filteredCrimes = type === 'ALL'
      ? crimes
      : crimes.filter(crime => crime.offense_description.includes(type));

    // Process data for regression analysis
    const dates = filteredCrimes.map(crime => new Date(crime.date));

    // If no data, return empty result
    if (dates.length === 0) {
      return res.json({
        status: 'success',
        message: 'No data available for prediction',
        data: {
          trend: 'neutral',
          predictionData: []
        }
      });
    }

    // Convert dates to numerical values (days since start)
    const firstDate = new Date(Math.min(...dates));
    const points = dates.map((date, i) => [
      Math.floor((date - firstDate) / (24 * 60 * 60 * 1000)), // x: days since start
      i // y: cumulative count
    ]);

    // Perform linear regression
    const regression = linearRegression(points);
    const regressionLine = linearRegressionLine(regression);

    // Generate prediction points for future dates
    const lastDate = new Date(Math.max(...dates));
    const futureDays = period === 'month' ? 30 : (period === 'week' ? 7 : 90);

    const predictionPoints = [];
    for (let i = 0; i <= futureDays; i++) {
      const x = Math.floor((lastDate - firstDate) / (24 * 60 * 60 * 1000)) + i;
      predictionPoints.push({
        x: new Date(firstDate.getTime() + x * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        y: regressionLine(x)
      });
    }

    // Determine trend direction based on slope
    let trend = 'neutral';
    if (regression.m > 0.05) trend = 'increasing';
    else if (regression.m < -0.05) trend = 'decreasing';

    res.json({
      status: 'success',
      data: {
        trend,
        slope: regression.m,
        predictionData: predictionPoints
      }
    });

  } catch (error) {
    console.error('Error in trend prediction:', error);
    res.status(500).json({
      status: 'error',
      message: 'Failed to predict trend',
      error: error.message
    });
  }
});

// Endpoint to predict crime hotspots
app.get('/api/predict/hotspots', async (req, res) => {
  try {
    // Get crime data
    const crimes = await fetchCrimeData();

    // Process location data
    const locations = crimes
      .filter(crime => crime.latitude && crime.longitude)
      .map(crime => ({
        lat: parseFloat(crime.latitude),
        lng: parseFloat(crime.longitude),
        type: crime.offense_description
      }));

    // Simple clustering to identify hotspots
    const hotspots = identifyHotspots(locations);

    res.json({
      status: 'success',
      data: {
        hotspots
      }
    });

  } catch (error) {
    console.error('Error in hotspot prediction:', error);
    res.status(500).json({
      status: 'error',
      message: 'Failed to predict hotspots',
      error: error.message
    });
  }
});

// Simple function to identify crime hotspots
function identifyHotspots(locations) {
  // This is a simplified version - in production, use proper clustering algorithms

  // Initialize grid
  const grid = {};

  // Grid size in degrees (approximately 100m)
  const gridSize = 0.001;

  // Count crimes in each grid cell
  locations.forEach(loc => {
    const gridX = Math.floor(loc.lng / gridSize);
    const gridY = Math.floor(loc.lat / gridSize);
    const key = `${gridX}-${gridY}`;

    if (!grid[key]) {
      grid[key] = {
        count: 0,
        lat: loc.lat,
        lng: loc.lng,
        types: {}
      };
    }

    grid[key].count++;
    grid[key].types[loc.type] = (grid[key].types[loc.type] || 0) + 1;
  });

  // Convert to array and sort by count
  const hotspots = Object.values(grid)
    .filter(cell => cell.count >= 3) // Minimum threshold
    .sort((a, b) => b.count - a.count)
    .slice(0, 20); // Top 20 hotspots

  return hotspots;
}

// Start the server
app.listen(PORT, () => {
  console.log(`ML service running on http://localhost:${PORT}`);
  init();
});

export default app; 