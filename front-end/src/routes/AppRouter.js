import React from "react";
import { BrowserRouter as Router, Route, Switch } from "react-router-dom";
import PrivateRoute from "./PrivateRoute";
import Login from "../components/Auth/Login";
import Register from "../components/Auth/Register";
import Dashboard from "../components/Pages/Dashboard";

const AppRouter = () => {
return (
    <Router>
        <Switch>
            <Route exact path="/" component={Login} />
            <Route exact path="/login" component={Login} />
            <Route exact path="/register" component={Register} />
            <PrivateRoute exact path="/dashboard" component={Dashboard} />
            <Route path="*" component={() => <h1>404 - Página no encontrada</h1>} />
        </Switch>
    </Router>
);
};

export default AppRouter;