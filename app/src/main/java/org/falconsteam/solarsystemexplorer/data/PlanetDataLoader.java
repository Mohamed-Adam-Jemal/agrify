package org.falconsteam.solarsystemexplorer.data;

import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import org.falconsteam.solarsystemexplorer.model.CelestialBody;
import org.falconsteam.solarsystemexplorer.model.DwarfPlanet;
import org.falconsteam.solarsystemexplorer.model.Planet;
import org.falconsteam.solarsystemexplorer.model.Star;

import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

public class PlanetDataLoader {

    public List<CelestialBody> loadAll() {
        List<CelestialBody> bodies = new ArrayList<>();
        try (InputStream is = getClass().getResourceAsStream("/data/planets.json")) {
            if (is == null) throw new RuntimeException("planets.json not found in resources");
            JsonArray arr = JsonParser.parseReader(new InputStreamReader(is)).getAsJsonArray();
            for (JsonElement el : arr) {
                JsonObject obj = el.getAsJsonObject();
                String name   = obj.get("name").getAsString();
                String type   = obj.get("type").getAsString();
                double dist   = obj.get("distanceFromSun").getAsDouble();
                double radius = obj.get("radiusKm").getAsDouble();
                String desc   = obj.get("description").getAsString();

                bodies.add(switch (type) {
                    case "Star"       -> new Star(name, dist, radius, desc);
                    case "DwarfPlanet"-> new DwarfPlanet(name, dist, radius, desc);
                    default           -> new Planet(name, dist, radius, desc);
                });
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return bodies;
    }
}