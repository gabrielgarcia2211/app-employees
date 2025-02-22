export const handleError = (error, defaultMessage) => {
  if (error.response?.status === 401) {
    window.location.href = '/login';
    return;
  }
  return (
    error.response?.data?.error ||
    error.response?.data?.message ||
    error.message ||
    defaultMessage
  );
};
