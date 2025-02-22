import React, { useEffect, useState } from "react";
import { getUserInfo } from "../../api/userService";
import { editEmployeePosition, deleteEmployee } from "../../api/employeService";
import { getPositions } from "../../api/positionService";
import "bootstrap/dist/css/bootstrap.min.css";
import Swal from "sweetalert2";
import Spinner from "../Layout/Spinner";
import { handleError } from "../../utils/errorHandler";
import { Toast } from "../../utils/toast";

const Perfil = () => {
  const [error, setError] = useState("");
  const [userInfo, setUserInfo] = useState(null);
  const [positions, setPositions] = useState([]);
  const [loading, setLoading] = useState(false);

  const fetchUserInfo = async () => {
    try {
      const data = await getUserInfo();
      setUserInfo(data);
    } catch (error) {
      setUserInfo(null);
    }
  };

  useEffect(() => {
    const fetchPositions = async () => {
      const response = await getPositions();
      setPositions(response["positions"]);
    };
    fetchUserInfo();
    fetchPositions();
  }, []);

  const handlePositionChange = (event) => {
    setUserInfo({ ...userInfo, position: event.target.value });
  };

  const handleEdit = async () => {
    try {
      setError("");
      setLoading(true);
      const { id, position } = userInfo;
      await editEmployeePosition(id, position);
      Toast.fire({
        icon: "success",
        title: "Información actualizada correctamente",
      });
      fetchUserInfo();
    } catch (error) {
      const errorMessage = handleError(error, "Error al registrar el usuario");
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async () => {
    try {
      setError("");
      setLoading(true);
      const { id } = userInfo;
      Swal.fire({
        title: "¿Deseas eliminar la información?",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
        buttonsStyling: false,
        customClass: {
          confirmButton: "btn btn-primary me-2",
          cancelButton: "btn btn-secondary",
        },
      }).then(async (result) => {
        if (result.isConfirmed) {
          await deleteEmployee(id);
          Toast.fire({
            icon: "success",
            title: "Información eliminada correctamente",
          });
        }
      });

      fetchUserInfo();
    } catch (error) {
      const errorMessage = handleError(error, "Error al eliminar el usuario");
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container mt-4">
      <div className="card">
        <div className="card-body">
          <h5 className="card-title">Perfil</h5>
          <p className="card-text text-secondary">Mi informacion</p>
          {error && <div className="alert alert-danger">{error}</div>}
          <form>
            <div className="mb-3">
              <label className="form-label">Nombre:</label>
              <input
                type="text"
                className="form-control"
                value={userInfo?.name}
                readOnly
                disabled
              />
            </div>
            <div className="mb-3">
              <label className="form-label">Apeelido:</label>
              <input
                type="text"
                className="form-control"
                value={userInfo?.lastname}
                readOnly
                disabled
              />
            </div>
            <div className="mb-3">
              <label className="form-label">Email:</label>
              <input
                type="email"
                className="form-control"
                value={userInfo?.email}
                readOnly
                disabled
              />
            </div>
            <div className="mb-3">
              <label className="form-label">Posición:</label>
              <select
                className="form-control"
                value={userInfo?.position}
                onChange={handlePositionChange}
              >
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
                type="text"
                className="form-control"
                value={userInfo?.birthdate}
                readOnly
                disabled
              />
            </div>
            <button
              type="button"
              className="btn btn-primary"
              onClick={handleEdit}
              style={{ marginRight: "10px" }}
              disabled={!userInfo || loading}
            >
              Actualizar
            </button>
            <button
              type="button"
              className="btn btn-danger"
              onClick={handleDelete}
              disabled={!userInfo || loading}
            >
              Eliminar
            </button>
            {loading && <Spinner />}
          </form>
        </div>
      </div>
    </div>
  );
};

export default Perfil;
