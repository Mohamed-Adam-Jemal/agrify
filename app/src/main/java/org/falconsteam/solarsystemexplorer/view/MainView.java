package org.falconsteam.solarsystemexplorer.view;

import org.falconsteam.solarsystemexplorer.model.CelestialBody;

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Label;
import javafx.scene.control.ListCell;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Priority;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.scene.shape.Circle;

public class MainView extends BorderPane {

    private final ListView<CelestialBody> listView = new ListView<>();
    private final TextField searchField = new TextField();

    public MainView() {
        getStyleClass().add("sidebar");

        // ── Header ──────────────────────────────────────────
        Label icon = new Label("🌌");
        icon.setStyle("-fx-font-size: 30px;");

        Label title = new Label("Solar System");
        title.getStyleClass().add("app-title");

        Label sub = new Label("Explorer");
        sub.getStyleClass().add("app-subtitle");

        VBox titleBox = new VBox(1, title, sub);
        HBox header = new HBox(10, icon, titleBox);
        header.setAlignment(Pos.CENTER_LEFT);
        header.setPadding(new Insets(20, 16, 16, 16));

        // ── Search ───────────────────────────────────────────
        searchField.setPromptText("🔍  Search bodies...");
        searchField.getStyleClass().add("search-field");
        searchField.setMaxWidth(Double.MAX_VALUE);

        HBox searchWrap = new HBox(searchField);
        searchWrap.setPadding(new Insets(0, 14, 10, 14));
        HBox.setHgrow(searchField, Priority.ALWAYS);

        // ── Section label ────────────────────────────────────
        Label section = new Label("ALL BODIES");
        section.getStyleClass().add("section-label");
        section.setPadding(new Insets(0, 16, 6, 16));

        // ── List ─────────────────────────────────────────────
        listView.getStyleClass().add("body-list");
        listView.setCellFactory(lv -> new ListCell<>() {
            @Override
            protected void updateItem(CelestialBody item, boolean empty) {
                super.updateItem(item, empty);
                setText(null);
                if (empty || item == null) {
                    setGraphic(null);
                } else {
                    setGraphic(buildCard(item, isSelected()));
                }
            }

            @Override
            public void updateSelected(boolean sel) {
                super.updateSelected(sel);
                if (getItem() != null) setGraphic(buildCard(getItem(), sel));
            }
        });

        VBox top = new VBox(header, searchWrap, section);
        top.getStyleClass().add("sidebar");
        setTop(top);
        setCenter(listView);
        setPrefWidth(240);
        VBox.setVgrow(listView, Priority.ALWAYS);
    }

    private HBox buildCard(CelestialBody item, boolean selected) {
        // ── Image ────────────────────────────────────────────
        ImageView img = new ImageView();
        img.setFitWidth(40);
        img.setFitHeight(40);
        img.setPreserveRatio(true);
        try {
            Image image = PlanetAssets.loadImage(item.getName(), 40, 40);
            if (image != null) img.setImage(image);
        } catch (Exception ignored) {}
        img.setClip(new Circle(20, 20, 20));

        StackPane imgWrap = new StackPane(img);
        imgWrap.getStyleClass().add("img-wrap");

        // ── Name ─────────────────────────────────────────────
        Label name = new Label(item.getName());
        name.getStyleClass().add(selected ? "card-name-selected" : "card-name");

        // ── Badge ─────────────────────────────────────────────
        Label badge = new Label(item.getType());
        badge.getStyleClass().addAll("badge", PlanetAssets.getBadgeClass(item));

        VBox info = new VBox(4, name, badge);
        info.setAlignment(Pos.CENTER_LEFT);

        // ── Card ──────────────────────────────────────────────
        HBox card = new HBox(12, imgWrap, info);
        card.setAlignment(Pos.CENTER_LEFT);
        card.setMaxWidth(Double.MAX_VALUE);
        card.getStyleClass().add(selected ? "planet-card-selected" : "planet-card");

        return card;
    }

    public ListView<CelestialBody> getListView() { return listView; }
    public TextField getSearchField()            { return searchField; }
}