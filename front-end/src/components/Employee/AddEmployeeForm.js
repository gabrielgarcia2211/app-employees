import React from "react";
import Spinner from "../Layout/Spinner";

const AddEmployeeForm = ({
  users,
  positions,
  newEmployee,
  selectedUser,
  handleInputChange,
  handleUserChange,
  handleAddEmployee,
  error,
  loading,
}) => {
  return (
    <div className="mt-4">
      <h5>Añadir Empleado</h5>
      <div className="form-group">
        <select
          className="form-control mb-2"
          value={selectedUser}
          onChange={handleUserChange}
        >
          <option value="">Seleccionar Usuario</option>
          {users.map((user) => (
            <option key={user.id} value={user.id}>
              {user.email}
            </option>
          ))}
        </select>
        <input
          type="text"
          name="name"
          className="form-control mb-2"
          placeholder="Nombre"
          value={newEmployee.name}
          onChange={handleInputChange}
        />
        <input
          type="text"
          name="lastname"
          className="form-control mb-2"
          placeholder="Apellido"
          value={newEmployee.lastname}
          onChange={handleInputChange}
        />
        <div className="mb-3">
          <select
            name="position"
            className="form-control"
            value={newEmployee.position}
            onChange={handleInputChange}
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
        <input
          type="date"
          name="birthdate"
          className="form-control mb-2"
          placeholder="Fecha de Nacimiento"
          value={newEmployee.birthdate}
          onChange={handleInputChange}
        />
        {error && <div className="alert alert-danger">{error}</div>}
        <button className="btn btn-success" onClick={handleAddEmployee}>
          Añadir
        </button>
        {loading && <Spinner />}
      </div>
    </div>
  );
};

export default AddEmployeeForm;
