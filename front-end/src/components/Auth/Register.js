import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import Swal from "sweetalert2";
import { register, login } from "../../api/authService"; // Importar la función login
import { getPositions } from "../../api/positionService";
import Spinner from "../Layout/Spinner";
import { handleError } from "../../utils/errorHandler";

const Register = () => {
  const [formData, setFormData] = useState({
    email: "",
    password: "",
    name: "",
    lastname: "",
    position: "",
    birthdate: "",
  });
  const [error, setError] = useState("");
  const [positions, setPositions] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchPositions = async () => {
      try {
        const response = await getPositions();
        if (response) {
          setPositions(response["positions"]);
        } else {
          setError("Error fetching positions");
        }
      } catch (error) {
        setError("Error fetching positions");
      }
    };

    fetchPositions();
  }, []);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      const response = await register(formData);
      Swal.fire("Registro exitoso", response.data.message, "success");
      setFormData({
        email: "",
        password: "",
        name: "",
        lastname: "",
        position: "",
        birthdate: "",
      });
      // Iniciar sesión automáticamente después del registro exitoso
      const loginResponse = await login(formData.email, formData.password);
      localStorage.setItem("token", loginResponse.data.token);
      localStorage.setItem("roles", JSON.stringify(loginResponse.data.user.roles));
      const userRoles = loginResponse.data.user.roles;
      if (userRoles.includes("ROLE_ADMIN")) {
        window.location.href = "/dashboard";
      } else if (userRoles.includes("ROLE_USER")) {
        window.location.href = "/perfil";
      }
    } catch (error) {
      const errorMessage = handleError(error, "Error al registrar el usuario");
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container mt-5">
      <h2>
        Registro{" "}
        <Link to="/login" className="btn btn-secondary">
          <i className="bi bi-arrow-left-circle"></i>
        </Link>
      </h2>
      {error && <div className="alert alert-danger">{error}</div>}
      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label className="form-label">Nombre</label>
          <input
            type="text"
            name="name"
            className="form-control"
            value={formData.name}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label className="form-label">Apellido</label>
          <input
            type="text"
            name="lastname"
            className="form-control"
            value={formData.lastname}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label className="form-label">Correo Electrónico</label>
          <input
            type="email"
            name="email"
            className="form-control"
            value={formData.email}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label className="form-label">Contraseña</label>
          <input
            type="password"
            name="password"
            className="form-control"
            value={formData.password}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label className="form-label">Posicion</label>
          <select
            name="position"
            className="form-control"
            value={formData.position}
            onChange={handleChange}
            required
          >
            <option value="">Seleccione una posición</option>
            {positions.map((position) => (
              <option key={position} value={position}>
                {position}
              </option>
            ))}
          </select>
        </div>
        <div className="mb-3">
          <label className="form-label">Fecha de Nacimiento:</label>
          <input
            type="date"
            name="birthdate"
            className="form-control"
            value={formData.birthdate}
            onChange={handleChange}
            required
          />
        </div>
        <button type="submit" className="btn btn-primary">
          Registrarse
        </button>
        {loading && <Spinner />}
      </form>
    </div>
  );
};

export default Register;
