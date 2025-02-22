import React from "react";
import "../../styles/employe.css";

const EmployeeList = ({ employees, onSearch }) => {
  const handleKeyUp = (event) => {
    onSearch(event.target.value);
  };

  return (
    <>
      <input
        type="text"
        placeholder="Filtrar por nombre..."
        onKeyUp={handleKeyUp}
        className="search-input"
      />
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
    </>
  );
};

export default EmployeeList;
