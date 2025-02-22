import React, { useEffect, useState } from "react";
import { getEmployees, addEmployee } from "../../api/employeService";
import { getUsers } from "../../api/userService";
import { getPositions } from "../../api/positionService";
import { handleError } from "../../utils/errorHandler";

const Dashboard = () => {
  const [employees, setEmployees] = useState([]);
  const [error, setError] = useState("");
  const [view, setView] = useState("list");
  const [newEmployee, setNewEmployee] = useState({
    name: "",
    lastname: "",
    position: "",
    birthdate: "",
  });
  const [users, setUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState("");
  const [positions, setPositions] = useState([]);

  useEffect(() => {
    const fetchEmployees = async () => {
      try {
        const response = await getEmployees();
        setEmployees(response);
      } catch (error) {
        const errorMessage = handleError(
          error,
          "Error al registrar el usuario"
        );
        setError(errorMessage);
      }
    };

    const fetchUsers = async () => {
      try {
        const response = await getUsers();
        setUsers(response);
      } catch (error) {
        const errorMessage = handleError(
          error,
          "Error al obtener los usuarios"
        );
        setError(errorMessage);
      }
    };

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

    fetchEmployees();
    fetchUsers();
    fetchPositions();
  }, []);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setNewEmployee({ ...newEmployee, [name]: value });
  };

  const handleUserChange = (e) => {
    setSelectedUser(e.target.value);
  };

  const handleAddEmployee = async () => {
    try {
      await addEmployee({ ...newEmployee, userId: selectedUser });
      setEmployees([...employees, newEmployee]);
      setNewEmployee({ name: "", lastname: "", position: "", birthdate: "" });
      setView("list");
    } catch (error) {
      const errorMessage = handleError(error, "Error al añadir el empleado");
      setError(errorMessage);
    }
  };

  return (
    <div className="container mt-4">
      <div className="card">
        <div className="card-body">
          <h5 className="card-title">Home</h5>
          <p className="card-text text-secondary">Control de Empleados!</p>
          <button
            className="btn btn-primary mb-3"
            onClick={() => setView(view === "list" ? "add" : "list")}
          >
            {view === "list" ? "Añadir Empleado" : "Ver Lista de Empleados"}
          </button>
          {view === "list" ? (
            <table className="table">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Posición</th>
                  <th>Fecha de Nacimiento</th>
                </tr>
              </thead>
              <tbody>
                {Array.isArray(employees) &&
                  employees.map((employee) => (
                    <tr key={employee.id}>
                      <td>{employee.name}</td>
                      <td>{employee.lastname}</td>
                      <td>{employee.position}</td>
                      <td>{employee.birthdate}</td>
                    </tr>
                  ))}
              </tbody>
            </table>
          ) : (
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
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
