define(
    [
        './routes.json',
        '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js',
    ],function(Routes, Router){
        Router.setRoutingData(Routes);

        return Router;
    }
);