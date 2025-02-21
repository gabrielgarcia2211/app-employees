import axios from "axios";

const API_URL = process.env.REACT_APP_API_URL;

export const login = (email, password) => {
  return axios.post(`${API_URL}/login_check`, { email, password });
};

export const register = (formData) => {
  return axios.post(`${API_URL}/register`, formData);
};
