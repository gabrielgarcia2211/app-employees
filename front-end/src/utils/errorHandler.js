export const handleError = (error, defaultMessage) => {
  return (
    error.response?.data?.error ||
    error.response?.data?.message ||
    error.message ||
    defaultMessage
  );
};
