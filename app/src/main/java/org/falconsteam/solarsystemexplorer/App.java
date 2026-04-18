package org.falconsteam.solarsystemexplorer;

import org.falconsteam.solarsystemexplorer.controller.MainController;
import org.falconsteam.solarsystemexplorer.view.DetailView;
import org.falconsteam.solarsystemexplorer.view.MainView;

import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.control.SplitPane;
import javafx.stage.Stage;

public class App extends Application {

    @Override
    public void start(Stage stage) {
        MainView   mainView   = new MainView();
        DetailView detailView = new DetailView();

        new MainController(mainView, detailView);

        SplitPane split = new SplitPane(mainView, detailView);
        split.setDividerPositions(0.28);

        Scene scene = new Scene(split, 980, 640);
        scene.getStylesheets().add(
            getClass().getResource("/styles/main.css").toExternalForm()
        );

        stage.setTitle("Solar System Explorer");
        stage.setMinWidth(720);
        stage.setMinHeight(500);
        stage.setScene(scene);
        stage.show();
    }

    public static void main(String[] args) { launch(args); }
}