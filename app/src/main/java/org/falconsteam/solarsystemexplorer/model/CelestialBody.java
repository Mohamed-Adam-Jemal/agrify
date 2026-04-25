package org.falconsteam.solarsystemexplorer.model;

public abstract class CelestialBody {
    protected String name;
    protected double distanceFromSun;   // in AU
    protected double radiusKm;
    protected String description;

    // New animation fields
    protected int    orbitalPeriodDays;
    protected double orbitalSpeedFactor;
    protected String color;
    protected int    displayRadius;

    public CelestialBody(String name, double distanceFromSun, double radiusKm, String description,
                         int orbitalPeriodDays, double orbitalSpeedFactor, String color, int displayRadius) {
        this.name              = name;
        this.distanceFromSun   = distanceFromSun;
        this.radiusKm          = radiusKm;
        this.description       = description;
        this.orbitalPeriodDays = orbitalPeriodDays;
        this.orbitalSpeedFactor = orbitalSpeedFactor;
        this.color             = color;
        this.displayRadius     = displayRadius;
    }

    // Existing getters
    public String getName()            { return name; }
    public double getDistanceFromSun() { return distanceFromSun; }
    public double getRadiusKm()        { return radiusKm; }
    public String getDescription()     { return description; }

    // New getters
    public int    getOrbitalPeriodDays()  { return orbitalPeriodDays; }
    public double getOrbitalSpeedFactor() { return orbitalSpeedFactor; }
    public String getColor()              { return color; }
    public int    getDisplayRadius()      { return displayRadius; }

    public abstract String getType();

    @Override
    public String toString() { return name; }
}