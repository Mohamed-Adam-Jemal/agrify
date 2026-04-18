package org.falconsteam.solarsystemexplorer.view;

import org.falconsteam.solarsystemexplorer.model.CelestialBody;

import javafx.geometry.Pos;
import javafx.scene.control.Label;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Region;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.scene.shape.Circle;

public class DetailView extends BorderPane {

    public DetailView() {
        getStyleClass().add("detail-panel");
        showPlaceholder();
    }

    private void showPlaceholder() {
        Label lbl = new Label("✦  Select a body to explore");
        lbl.getStyleClass().add("placeholder-text");
        StackPane p = new StackPane(lbl);
        p.getStyleClass().add("detail-panel");
        setCenter(p);
    }

    public void display(CelestialBody body) {
        // ── Image ─────────────────────────────────────────────
        ImageView img = new ImageView();
        img.setFitWidth(160);
        img.setFitHeight(160);
        img.setPreserveRatio(true);
        try {
            Image image = PlanetAssets.loadImage(body.getName(), 160, 160);
            if (image != null) img.setImage(image);
        } catch (Exception ignored) {}
        img.setClip(new Circle(80, 80, 80));

        StackPane imgWrap = new StackPane(img);
        imgWrap.getStyleClass().add("detail-img-wrap");

        // ── Name + badge ──────────────────────────────────────
        Label name = new Label(body.getName());
        name.getStyleClass().add("detail-name");

        Label badge = new Label("  " + body.getType() + "  ");
        badge.getStyleClass().addAll("detail-badge", PlanetAssets.getBadgeClass(body));

        VBox nameBlock = new VBox(8, name, badge);
        nameBlock.setAlignment(Pos.CENTER_LEFT);

        HBox hero = new HBox(28, imgWrap, nameBlock);
        hero.setAlignment(Pos.CENTER_LEFT);

        // ── Divider ───────────────────────────────────────────
        Region div = new Region();
        div.getStyleClass().add("divider");
        div.setMaxWidth(Double.MAX_VALUE);

        // ── About ─────────────────────────────────────────────
        Label aboutLabel = new Label("ABOUT");
        aboutLabel.getStyleClass().add("detail-about-label");

        Label desc = new Label(body.getDescription());
        desc.getStyleClass().add("detail-desc");
        desc.setMaxWidth(480);
        desc.setWrapText(true);

        // ── Stats ─────────────────────────────────────────────
        Label statsLabel = new Label("STATS");
        statsLabel.getStyleClass().add("detail-about-label");

        HBox stats = new HBox(16,
            buildStat("Distance from Sun",
                body.getDistanceFromSun() == 0
                    ? "0.00" : String.format("%.2f", body.getDistanceFromSun()),
                "AU"),
            buildStat("Radius",
                String.format("%,.0f", body.getRadiusKm()),
                "km")
        );

        // ── Assemble ──────────────────────────────────────────
        VBox content = new VBox(20, hero, div, aboutLabel, desc, statsLabel, stats);
        content.getStyleClass().add("detail-panel");
        setCenter(content);
    }

    private VBox buildStat(String label, String value, String unit) {
        Label lbl = new Label(label.toUpperCase());
        lbl.getStyleClass().add("stat-label");

        Label val = new Label(value);
        val.getStyleClass().add("stat-value");

        Label unt = new Label(unit);
        unt.getStyleClass().add("stat-unit");

        VBox card = new VBox(4, lbl, val, unt);
        card.getStyleClass().add("stat-card");
        return card;
    }
}