package org.falconsteam.solarsystemexplorer.model;

public class DwarfPlanet extends CelestialBody {
    public DwarfPlanet(String name, double distanceFromSun, double radiusKm, String description) {
        super(name, distanceFromSun, radiusKm, description);
    }
    @Override public String getType() { return "Dwarf Planet"; }
}