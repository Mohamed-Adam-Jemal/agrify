package org.falconsteam.solarsystemexplorer.model;

public abstract class CelestialBody {
    protected String name;
    protected double distanceFromSun; // in AU
    protected double radiusKm;
    protected String description;

    public CelestialBody(String name, double distanceFromSun, double radiusKm, String description) {
        this.name = name;
        this.distanceFromSun = distanceFromSun;
        this.radiusKm = radiusKm;
        this.description = description;
    }

    public String getName() { return name; }
    public double getDistanceFromSun() { return distanceFromSun; }
    public double getRadiusKm() { return radiusKm; }
    public String getDescription() { return description; }

    public abstract String getType();

    @Override
    public String toString() { return name; }
}