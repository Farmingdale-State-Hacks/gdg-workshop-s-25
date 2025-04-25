from flask import Flask, jsonify, request
from flask_cors import CORS
import os
from google.cloud import bigquery
from google.cloud import storage
import pandas as pd
import json

app = Flask(__name__)
CORS(app)

# Initialize Google Cloud clients
bigquery_client = bigquery.Client()
storage_client = storage.Client()


@app.route("/api/health", methods=["GET"])
def health_check():
    return jsonify({"status": "healthy", "message": "DetectiveCase API is running"})


@app.route("/api/crimes", methods=["GET"])
def get_crimes():
    """Fetches crime data based on query parameters"""
    try:
        # Extract query parameters
        crime_type = request.args.get("type", "ALL")
        start_date = request.args.get("start_date", "2019-01-01")
        end_date = request.args.get("end_date", "2023-01-01")
        limit = int(request.args.get("limit", 1000))

        # Build the BigQuery SQL query
        query = f"""
        SELECT 
            CMPLNT_NUM as complaint_id,
            OFNS_DESC as offense_description,
            BORO_NM as borough,
            CMPLNT_FR_DT as date,
            Latitude,
            Longitude,
            PREM_TYP_DESC as premises_type
        FROM `project.dataset.nypd_complaints`
        WHERE CMPLNT_FR_DT BETWEEN '{start_date}' AND '{end_date}'
        """

        # Add crime type filter if not ALL
        if crime_type != "ALL":
            query += f" AND OFNS_DESC = '{crime_type}'"

        query += f" LIMIT {limit}"

        # Execute the query
        query_job = bigquery_client.query(query)
        results = query_job.result()

        # Convert to list of dicts
        crimes = [dict(row) for row in results]

        return jsonify({"status": "success", "count": len(crimes), "data": crimes})

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


@app.route("/api/crime-types", methods=["GET"])
def get_crime_types():
    """Returns the list of all crime types for filtering"""
    try:
        query = """
        SELECT DISTINCT OFNS_DESC as crime_type
        FROM `project.dataset.nypd_complaints`
        WHERE OFNS_DESC IS NOT NULL
        ORDER BY OFNS_DESC
        """

        query_job = bigquery_client.query(query)
        results = query_job.result()

        crime_types = [row["crime_type"] for row in results]

        return jsonify(
            {"status": "success", "count": len(crime_types), "data": crime_types}
        )

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


@app.route("/api/crime-heatmap", methods=["GET"])
def get_crime_heatmap():
    """Returns heatmap data for crimes"""
    try:
        crime_type = request.args.get("type", "ALL")
        period = request.args.get(
            "period", "pre-covid"
        )  # pre-covid, during-covid, post-covid

        # Define date ranges for periods
        periods = {
            "pre-covid": ("2019-01-01", "2020-02-29"),
            "during-covid": ("2020-03-01", "2021-06-30"),
            "post-covid": ("2021-07-01", "2023-01-01"),
        }

        start_date, end_date = periods.get(period, periods["pre-covid"])

        query = f"""
        SELECT 
            Latitude,
            Longitude,
            COUNT(*) as count
        FROM `project.dataset.nypd_complaints`
        WHERE CMPLNT_FR_DT BETWEEN '{start_date}' AND '{end_date}'
        AND Latitude IS NOT NULL
        AND Longitude IS NOT NULL
        """

        if crime_type != "ALL":
            query += f" AND OFNS_DESC = '{crime_type}'"

        query += """
        GROUP BY Latitude, Longitude
        """

        query_job = bigquery_client.query(query)
        results = query_job.result()

        heatmap_data = [
            {"lat": row["Latitude"], "lng": row["Longitude"], "weight": row["count"]}
            for row in results
        ]

        return jsonify(
            {
                "status": "success",
                "period": period,
                "period_dates": {"start": start_date, "end": end_date},
                "count": len(heatmap_data),
                "data": heatmap_data,
            }
        )

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


@app.route("/api/neighborhood-stats", methods=["GET"])
def get_neighborhood_stats():
    """Returns crime statistics by neighborhood"""
    try:
        query = """
        SELECT 
            BORO_NM as borough,
            COUNT(*) as total_crimes,
            COUNT(CASE WHEN OFNS_DESC LIKE '%THEFT%' THEN 1 END) as theft_count,
            COUNT(CASE WHEN OFNS_DESC LIKE '%ASSAULT%' THEN 1 END) as assault_count,
            COUNT(CASE WHEN OFNS_DESC LIKE '%BURGLARY%' THEN 1 END) as burglary_count
        FROM `project.dataset.nypd_complaints`
        WHERE CMPLNT_FR_DT BETWEEN '2019-01-01' AND '2023-01-01'
        GROUP BY BORO_NM
        ORDER BY total_crimes DESC
        """

        query_job = bigquery_client.query(query)
        results = query_job.result()

        stats = [dict(row) for row in results]

        return jsonify({"status": "success", "count": len(stats), "data": stats})

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 8002))
    app.run(host="0.0.0.0", port=port, debug=True)
