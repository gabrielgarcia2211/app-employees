import axios from "axios";

const API_URL = process.env.REACT_APP_API_URL;

export const getEmployees = async () => {
  const token = localStorage.getItem("token");
  const response = await axios.get(`${API_URL}/employees`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });
  return response.data;
};

export const addEmployee = async (employee) => {
  const token = localStorage.getItem("token");
  const response = await axios.post(`${API_URL}/employees`, employee, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });
  return response.data;
};
