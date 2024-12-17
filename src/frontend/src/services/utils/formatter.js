export const onlyNumbers = (val) => {
  return val.replace(/[^0-9]/g, '');
};

export const formatPhoneNumber = (phoneNumber) => {
  if (!phoneNumber) {
    return phoneNumber;
  }

  // Remove all non-numeric characters
  phoneNumber = phoneNumber.replace(/\D/g, '');

  // Split the number into parts: area code, prefix, and suffix
  const areaCode = phoneNumber.slice(0, 2);
  const firstPart = phoneNumber.length === 11 ? phoneNumber.slice(2, 7) : phoneNumber.slice(2, 6); // 9 or 8 digits
  const lastPart = phoneNumber.length === 11 ? phoneNumber.slice(7) : phoneNumber.slice(6);

  // Return the formatted number
  return `(${areaCode}) ${firstPart}-${lastPart}`;
};
