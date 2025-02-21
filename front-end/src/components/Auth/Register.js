import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import Swal from "sweetalert2";
import { register } from "../../api/authService";
import { getPositions } from "../../api/positionService";
import Spinner from "../Layout/Spinner"; // Importar el componente Spinner

const Register = () => {
  const [formData, setFormData] = useState({
    email: "",
    password: "",
    name: "",
    lastname: "",
    position: "",
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
      Swal.fire("Login exitoso", response.data.message, "success");
      setFormData({
        email: "",
        password: "",
        name: "",
        lastname: "",
        position: "",
      });
    } catch (error) {
      const errorMessage =
        error.response?.data?.error ||
        error.message ||
        "Error al registrar el usuario";
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
        <button type="submit" className="btn btn-primary">
          Registrarse
        </button>
        {loading && <Spinner />}
      </form>
    </div>
  );
};

export default Register;
