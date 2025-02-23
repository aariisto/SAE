from flask import Flask, render_template, jsonify
from flask_cors import CORS
import requests
import re


app = Flask(__name__)
CORS(app)


@app.route('/stations', methods=['GET'])
def get_stations():
    # Effectuer une requête GET pour obtenir les informations des stations
    response = requests.get("https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json")
    
    # Vérifier si la requête a réussi
    if response.status_code == 200:
        # Convertir la réponse JSON en dictionnaire Python
        data = response.json()
        formatted_data = []

        # Filtrer et structurer les données des stations
        for station in data.get('data', {}).get('stations', []):
            # Valider les champs et types requis pour chaque station
            if (
                isinstance(station.get('station_id'), int) and
                isinstance(station.get('stationCode'), str) and
                isinstance(station.get('name'), str) and
                isinstance(station.get('lat'), float) and
                isinstance(station.get('lon'), float) and
                isinstance(station.get('capacity'), int)
            ):
                # Ajouter la station avec les champs formatés dans la liste finale
                formatted_station = {
                    "station_id": station['station_id'],
                    "stationCode": station['stationCode'],
                    "name": station['name'],
                    "lat": station['lat'],
                    "lon": station['lon'],
                    "capacity": station['capacity']
                }
                formatted_data.append(formatted_station)

        # Retourner les données formatées en JSON
        return jsonify(formatted_data)
    else:
        # Retourner une erreur si la requête a échoué
        return jsonify({"error": f"Erreur lors de la requête: {response.status_code}"}), response.status_code


@app.route('/station/<int:station_id>', methods=['GET'])
def get_station_info(station_id):
    # Effectuer une requête GET pour obtenir les informations de statut des stations
    response = requests.get("https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_status.json")
    
    # Vérifier si la requête a réussi
    if response.status_code == 200:
        # Convertir la réponse JSON en dictionnaire Python
        data = response.json()
        
        # Trouver la station avec le station_id donné
        station_info = next((station for station in data['data']['stations'] if station['station_id'] == station_id), None)
        
        # Si la station est trouvée, retourner ses informations
        if station_info:
            return jsonify(station_info)
        else:
            # Retourner une réponse vide si la station n'est pas trouvée
            return jsonify(None)
    else:
        # Retourner une erreur si la requête a échoué
        return jsonify({"error": f"Erreur lors de la requête: {response.status_code}"}), response.status_code 


@app.route('/search_station/<string:name>', methods=['GET'])
def search_station(name):
    # Effectuer une requête GET pour obtenir les informations des stations
    response = requests.get("https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json")
    
    # Vérifier si la requête a réussi
    if response.status_code == 200:
        # Convertir la réponse JSON en dictionnaire Python
        stations_data = response.json()
        
        # Parcourir les stations pour trouver une correspondance avec le nom donné
        for station in stations_data['data']['stations']:
            # Comparer les noms des stations en ignorant les espaces, tirets et majuscules
            if (re.sub(r'[\s_-]+', ' ', name).strip().lower() == re.sub(r'[\s_-]+', ' ', station['name']).strip().lower()):
                # Retourner les informations de la station trouvée
                return jsonify({
                    "station_id": station['station_id'],
                    "lat": station['lat'],
                    "lon": station['lon']
                })
    else:
        # Retourner une erreur si la requête a échoué
        return jsonify({"error": "Erreur lors de la requête"}), response.status_code
    
    """
    # Préparer le nom pour la requête à l'API Google
    name_google = name.replace(' ', '+')
    api_key = "AIzaSyDUm17xB5A406D0Z81JeCEaPT7HiWK1Qe4"
    url = f'https://maps.googleapis.com/maps/api/geocode/json?address={name_google}&key={api_key}'

    # Effectuer une requête GET à l'API Google pour obtenir les coordonnées
    response = requests.get(url)
    
    # Vérifier si la requête a réussi
    if response.status_code == 200:
        data = response.json()
        
        # Vérifier si la réponse de l'API Google est valide
        if data['status'] == 'OK':
            lat = data['results'][0]['geometry']['location']['lat']
            lng = data['results'][0]['geometry']['location']['lng']
            
            # Retourner les coordonnées obtenues de l'API Google
            return jsonify({
                "station_id": None,
                "lat": lat,
                "lon": lng
            })
    else:
        # Retourner une erreur si la requête a échoué
        return jsonify({"error": "Erreur lors de la requête"}), response.status_code
    """
    
    # Retourner une erreur si aucune station n'est trouvée
    return jsonify({"error": "Station non trouvée"})
    
    
    

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)