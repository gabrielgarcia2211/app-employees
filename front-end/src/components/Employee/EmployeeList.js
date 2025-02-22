import React, { useState, useEffect } from "react";
import "../../styles/employe.css";

const EmployeeList = ({ employees, onSearch, editName }) => {
  const [editedEmployees, setEditedEmployees] = useState([]);

  useEffect(() => {
    setEditedEmployees(employees);
  }, [employees]);

  const handleKeyUp = (event) => {
    onSearch(event.target.value);
  };

  const handleNameChange = (id, newName) => {
    setEditedEmployees((prevEmployees) =>
      prevEmployees.map((employee) =>
        employee.id === id ? { ...employee, name: newName } : employee
      )
    );
  };

  const handleNameBlur = (id, name) => {
    const originalEmployee = employees.find((employee) => employee.id === id);
    if (originalEmployee && name !== originalEmployee.name) {
      editName({
        data: { id, name },
        type: "name",
      });
    }
  };

  return (
    <>
      {" "}
      <input
        type="text"
        placeholder="Filtrar por nombre..."
        onKeyUp={handleKeyUp}
        className="search-input"
      />
      <div>
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
            {Array.isArray(editedEmployees) &&
              editedEmployees.map((employee) => (
                <tr key={employee.id}>
                  <td>
                    <input
                      type="text"
                      value={employee.name}
                      onChange={(e) =>
                        handleNameChange(employee.id, e.target.value)
                      }
                      onBlur={(e) =>
                        handleNameBlur(employee.id, e.target.value)
                      }
                      className="table-input"
                    />
                  </td>
                  <td>{employee.lastname}</td>
                  <td>{employee.position}</td>
                  <td>{employee.birthdate}</td>
                </tr>
              ))}
          </tbody>
        </table>
      </div>
    </>
  );
};

export default EmployeeList;
