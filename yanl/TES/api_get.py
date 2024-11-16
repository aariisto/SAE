from flask import Flask, render_template, jsonify
import requests
import re
app = Flask(__name__)



@app.route('/stations', methods=['GET'])
def get_stations():
    response = requests.get("https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json")
    if response.status_code == 200:
        data = response.json()
        return jsonify(data)
    else:
        return jsonify({"error": f"Erreur lors de la requête: {response.status_code}"}), response.status_code
    