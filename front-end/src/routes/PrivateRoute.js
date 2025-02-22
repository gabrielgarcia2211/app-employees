import React from "react";
import { Route, Redirect } from "react-router-dom";

const PrivateRoute = ({ component: Component, roles, ...rest }) => {
  const token = localStorage.getItem("token");
  const userRoles = JSON.parse(localStorage.getItem("roles")) || [];

  return (
    <Route
      {...rest}
      render={(props) =>
        token && roles.some((role) => userRoles.includes(role)) ? (
          <Component {...props} />
        ) : (
          <Redirect to="/login" />
        )
      }
    />
  );
};

export default PrivateRoute;