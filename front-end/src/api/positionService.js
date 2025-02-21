import axios from "axios";

const API_URL = process.env.REACT_APP_URL;

export const getPositions = async () => {
    try {
        const response = await axios.get(`${API_URL}/positions`);
        return response.data;
    } catch (error) {
        console.error("Error fetching positions:", error);
    }
};
