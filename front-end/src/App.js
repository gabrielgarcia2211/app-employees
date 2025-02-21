import React from "react";
import { BrowserRouter as Router, Route, Switch, Redirect } from "react-router-dom";
import Register from "./components/Auth/Register";
import Login from "./components/Auth/Login";

function App() {
  return (
    <Router>
      <Switch>
        <Route path="/register" component={Register} />
        <Route path="/login" component={Login} />
        <Redirect from="/" to="/login" />
      </Switch>
    </Router>
  );
}

export default App;