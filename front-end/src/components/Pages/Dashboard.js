import React, { useEffect, useState } from "react";
import {
  getEmployees,
  addEmployee,
  editEmployeeName,
} from "../../api/employeService";
import { getUsers } from "../../api/userService";
import { getPositions } from "../../api/positionService";
import { handleError } from "../../utils/errorHandler";
import EmployeeList from "../Employee/EmployeeList";
import AddEmployeeForm from "../Employee/AddEmployeeForm";
import Swal from "sweetalert2";

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
  const [loading, setLoading] = useState(false);

  const fetchEmployees = async (search = null) => {
    try {
      const response = await getEmployees(search);
      setEmployees(response);
    } catch (error) {
      setEmployees([]);
      const errorMessage = handleError(error, "Error al registrar el usuario");
      setError(errorMessage);
    }
  };

  useEffect(() => {
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
    fetchPositions();
    fetchUsers();
  }, []);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setNewEmployee({ ...newEmployee, [name]: value });
  };

  const handleUserChange = (e) => {
    setSelectedUser(e.target.value);
  };

  const handleAddEmployee = async () => {
    setError("");
    setLoading(true);
    try {
      await addEmployee({ ...newEmployee, user_id: selectedUser });
      setView("list");
      fetchEmployees();
    } catch (error) {
      const errorMessage = handleError(error, "Error al añadir el empleado");
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = async (search) => {
    fetchEmployees(search);
  };

  const handleEdit = async (data) => {
    try {
      const { id, name } = data.data;
      await editEmployeeName(id, name);
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener("mouseenter", Swal.stopTimer);
          toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: "success",
        title: "Empleado actualizado correctamente",
      });
      fetchEmployees();
    } catch (error) {
      const errorMessage = handleError(error, "Error al registrar el usuario");
      setError(errorMessage);
    }
  };

  return (
    <div className="container mt-4">
      <div className="card">
        <div className="card-body">
          <h5 className="card-title">Home</h5>
          <p className="card-text text-secondary">Control de Empleados</p>
          <button
            className="btn btn-primary mb-3"
            onClick={() => setView(view === "list" ? "add" : "list")}
          >
            {view === "list" ? (
              <i class="bi bi-plus-circle-fill"> Añadir Empleado</i>
            ) : (
              <i class="bi bi-list-task"> Lista de Empleados</i>
            )}
          </button>
          {view === "list" ? (
            <EmployeeList
              employees={employees}
              onSearch={handleSearch}
              editName={handleEdit}
            />
          ) : (
            <AddEmployeeForm
              users={users}
              positions={positions}
              newEmployee={newEmployee}
              selectedUser={selectedUser}
              handleInputChange={handleInputChange}
              handleUserChange={handleUserChange}
              handleAddEmployee={handleAddEmployee}
              error={error}
              loading={loading}
            />
          )}
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
