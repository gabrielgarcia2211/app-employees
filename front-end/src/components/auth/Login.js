import React, { useState } from "react";
import { login } from "../../api/authService";
import { Link } from "react-router-dom";
import "../../styles/login.css";

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    try {
      const response = await login(email, password);
      localStorage.setItem("token", response.data.token);
      localStorage.setItem("roles", JSON.stringify(response.data.user.roles));
      const userRoles = response.data.user.roles;
      if (userRoles.includes("ROLE_ADMIN")) {
        window.location.href = "/dashboard";
      } else if (userRoles.includes("ROLE_USER")) {
        window.location.href = "/perfil";
      }
    } catch (error) {
      setError("Credenciales incorrectas");
    }
  };

  return (
    <div
      className="card container mt-5 p-4 shadow-lg"
      style={{ maxWidth: "400px", width: "100%", position: "relative" }}
    >
      <img src="/logo.png" className="img-fluid mt-3 logo-img" />
      <h2 className="text-center mb-3">Iniciar Sesión</h2>

      {error && <div className="alert alert-danger">{error}</div>}

      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label className="form-label">Correo Electrónico</label>
          <input
            type="email"
            className="form-control"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
        </div>

        <div className="mb-3">
          <label className="form-label">Contraseña</label>
          <input
            type="password"
            className="form-control"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
        </div>

        <button type="submit" className="btn btn-primary w-100">
          Iniciar Sesión
        </button>
      </form>
      <div className="mt-3">
        <Link to="/register" className="btn btn-secondary w-100">
          Registrarse
        </Link>
      </div>
    </div>
  );
};

export default Login;
