// Get the form element and attach a submit event listener
const forms = document.getElementById('myForm');
forms.addEventListener('submit', function (event) {
  // Prevent the form from being submitted
  event.preventDefault();

  const firstnameInput = document.getElementById('firstname');
  const firstnameError = document.getElementById('firstname-error');
  const firstnameValue = firstnameInput.value.trim();

  const middlenameInput = document.getElementById('middlename');
  const middlenameError = document.getElementById('middlename-error');
  const middlenameValue = middlenameInput.value.trim();

  const surnameInput = document.getElementById('surname');
  const surnameError = document.getElementById('surname-error');
  const surnameValue = surnameInput.value.trim();

  const emailInput = document.getElementById('email');
  const emailError = document.getElementById('email-error');
  const emailValue = emailInput.value.trim();
  const emailRegex = /^[A-Za-z0-9._%+-]+@cvsu\.edu\.ph$/;

  const studentNumberInput = document.getElementById('student-number');
  const studentNumberError = document.getElementById('student-number-error');
  const studentNumberValue = studentNumberInput.value.trim();
  const studentNumberRegex = /^\d{9}$/;

  const courseInput = document.getElementById('course');
  const courseContainer = document.getElementById('select');
  const courseError = document.getElementById('course-error');
  const courseValue = courseInput.value;

  const componentInput = document.getElementById('component_id');
  const componentContainer = document.getElementById('select');
  const componentError = document.getElementById('component-error');
  const componentValue = componentInput.value;

  const yearLevelInput = document.getElementById('year-level');
  const yearLevelContainer = document.getElementById('select');
  const yearLevelError = document.getElementById('year-level-error');
  const yearLevelValue = yearLevelInput.value;

  const sexInput = document.getElementById('sex');
  const sexContainer = document.getElementById('select');
  const sexError = document.getElementById('sex-error');
  const sexValue = sexInput.value;

  const birthmonthInput = document.getElementById('birthday-month');
  const birthmonthContainer = document.getElementById('select');
  const birthmonthError = document.getElementById('birthday-month-error');
  const birthmonthValue = birthmonthInput.value;

  const birthdayInput = document.getElementById('birthday-day');
  const birthdayContainer = document.getElementById('select');
  const birthdayError = document.getElementById('birthday-day-error');
  const birthdayValue = birthdayInput.value;

  const birthyearInput = document.getElementById('birthday-year');
  const birthyearContainer = document.getElementById('select');
  const birthyearError = document.getElementById('birthday-year-error');
  const birthyearValue = birthyearInput.value;

  const contactNumberInput = document.getElementById('contact-number');
  const contactNumberError = document.getElementById('contact-number-error');
  const contactNumberValue = contactNumberInput.value.trim();
  const contactNumberRegex = /^(09|\+639)\d{9}$/;

  const barangayInput = document.getElementById('street-barangay');
  const barangayError = document.getElementById('street-barangay-error');
  const barangayValue = barangayInput.value.trim();

  const cityInput = document.getElementById('city-municipality');
  const cityError = document.getElementById('city-municipality-error');
  const cityValue = cityInput.value.trim();

  const provinceInput = document.getElementById('province');
  const provinceError = document.getElementById('province-error');
  const provinceValue = provinceInput.value.trim();

  const passwordInput = document.getElementById('password');
  const passwordError = document.getElementById('password-error');
  const passwordValue = passwordInput.value;

  const re_passwordInput = document.getElementById('re_password');
  const re_passwordError = document.getElementById('re_password-error');
  const re_passwordValue = re_passwordInput.value;

  if (firstnameValue === '') {
    firstnameError.textContent = 'First Name is required';
    firstnameInput.classList.add('invalid');
    firstnameInput.focus();
    setTimeout(function () {
      firstnameError.textContent = '';
      firstnameInput.classList.remove('invalid');
    }, 3000);
  } else {
    firstnameError.textContent = '';
    firstnameInput.classList.remove('invalid');

    // if (middlenameValue === '') {
    //   middlenameError.textContent = 'Middle Name is required';
    //   middlenameInput.classList.add('invalid');
    //   middlenameInput.focus();
    //   setTimeout(function () {
    //     middlenameError.textContent = '';
    //     middlenameInput.classList.remove('invalid');
    //   }, 3000);
    // } else {
    //   middlenameError.textContent = '';
    //   middlenameInput.classList.remove('invalid');

    if (surnameValue === '') {
      surnameError.textContent = 'Last Name is required';
      surnameInput.classList.add('invalid');
      surnameInput.focus();
      setTimeout(function () {
        surnameError.textContent = '';
        surnameInput.classList.remove('invalid');
      }, 3000);
    } else {
      surnameError.textContent = '';
      surnameInput.classList.remove('invalid');

      if (emailValue === '') {
        emailError.textContent = 'Email is required';
        emailInput.classList.add('invalid');
        emailInput.focus();
        setTimeout(function () {
          emailError.textContent = '';
          emailInput.classList.remove('invalid');
        }, 3000); // Display the error message for 3 seconds
      } else if (!emailRegex.test(emailValue)) {
        emailError.textContent = 'Please enter a valid CvSU email address';
        emailInput.classList.add('invalid');
        emailInput.focus();
        setTimeout(function () {
          emailError.textContent = '';
          emailInput.classList.remove('invalid');
        }, 3000); // Display the error message for 3 seconds
      } else {
        emailError.textContent = '';
        emailInput.classList.remove('invalid');

        if (studentNumberValue === '') {
          studentNumberError.textContent = 'Student number is required';
          studentNumberInput.classList.add('invalid');
          studentNumberInput.focus();
          setTimeout(function () {
            studentNumberError.textContent = '';
            studentNumberInput.classList.remove('invalid');
          }, 3000); // Display the error message for 3 seconds
        } else if (!studentNumberRegex.test(studentNumberValue)) {
          studentNumberError.textContent =
            'Please enter a valid student number';
          studentNumberInput.classList.add('invalid');
          studentNumberInput.focus();
          setTimeout(function () {
            studentNumberError.textContent = '';
            studentNumberInput.classList.remove('invalid');
          }, 3000); // Display the error message for 3 seconds
        } else {
          // Student number is valid, remove any error messages

          studentNumberError.textContent = '';
          studentNumberInput.classList.remove('invalid');

          if (courseValue === '') {
            courseError.textContent = 'Course is required';
            //   courseContainer.classList.add('invalid');
            setTimeout(function () {
              courseError.textContent = '';
              // courseContainer.classList.remove('invalid');
              courseContainer.focus();
            }, 3000); // Display the error message for 3 seconds
          } else {
            courseError.textContent = '';
            //   courseContainer.classList.remove('invalid');

            if (componentValue === '') {
              componentError.textContent = 'Component is required';
              // componentContainer.classList.add('invalid');
              setTimeout(function () {
                componentError.textContent = '';
                //   componentContainer.classList.remove('invalid');
              }, 3000); // Display the error message for 3 seconds
            } else {
              // Component is valid, remove any error messages and submit the form
              componentError.textContent = '';
              // componentContainer.classList.remove('invalid');

              if (yearLevelValue === '') {
                yearLevelError.textContent = 'Year level is required';
                //   yearLevelContainer.classList.add('invalid');
                setTimeout(function () {
                  yearLevelError.textContent = '';
                  // yearLevelContainer.classList.remove('invalid');
                }, 3000); // Display the error message for 3 seconds
              } else {
                // yearLevel is valid, remove any error messages and submit the form
                yearLevelError.textContent = '';
                //   yearLevelContainer.classList.remove('invalid');

                if (sexValue === '') {
                  sexError.textContent = 'Gender is required';
                  // sexContainer.classList.add('invalid');
                  setTimeout(function () {
                    sexError.textContent = '';
                    //   sexContainer.classList.remove('invalid');
                  }, 3000); // Display the error message for 3 seconds
                } else {
                  // sex is valid, remove any error messages and submit the form
                  sexError.textContent = '';
                  // sexContainer.classList.remove('invalid');

                  if (birthmonthValue === '') {
                    birthmonthError.textContent = 'Birth Month is required';
                    //   birthmonthContainer.classList.add('invalid');
                    setTimeout(function () {
                      birthmonthError.textContent = '';
                      // birthmonthContainer.classList.remove('invalid');
                    }, 3000); // Display the error message for 3 seconds
                  } else {
                    // birthmonth is valid, remove any error messages and submit the form
                    birthmonthError.textContent = '';
                    //   birthmonthContainer.classList.remove('invalid');

                    if (birthdayValue === '') {
                      birthdayError.textContent = 'Birth Day is required';
                      // birthdayContainer.classList.add('invalid');
                      setTimeout(function () {
                        birthdayError.textContent = '';
                        //   birthdayContainer.classList.remove('invalid');
                      }, 3000); // Display the error message for 3 seconds
                    } else if (
                      parseInt(birthdayValue) < 1 ||
                      parseInt(birthdayValue) > 31
                    ) {
                      birthdayError.textContent =
                        'Please enter a valid day between 1 and 31';
                      setTimeout(function () {
                        birthdayError.textContent = '';
                        //   birthdayContainer.classList.remove('invalid');
                      }, 3000); // Display the error message for 3 seconds
                    } else {
                      // birthday is valid, remove any error messages and submit the form
                      birthdayError.textContent = '';
                      // birthdayContainer.classList.remove('invalid');

                      if (birthyearValue === '') {
                        birthyearError.textContent = 'Birth Year is required';
                        //   birthyearContainer.classList.add('invalid');
                        setTimeout(function () {
                          birthyearError.textContent = '';
                          // birthyearContainer.classList.remove('invalid');
                        }, 3000); // Display the error message for 3 seconds
                      } else {
                        // birthyear is valid, remove any error messages and submit the form
                        birthyearError.textContent = '';
                        //   birthyearContainer.classList.remove('invalid');

                        if (contactNumberValue === '') {
                          contactNumberError.textContent =
                            'Contact number is required';
                          contactNumberInput.classList.add('invalid');
                          setTimeout(function () {
                            contactNumberError.textContent = '';
                            contactNumberInput.classList.remove('invalid');
                          }, 3000); // Display the error message for 3 seconds
                        } else if (
                          !contactNumberRegex.test(contactNumberValue)
                        ) {
                          contactNumberError.textContent =
                            'Please enter a valid contact number';
                          contactNumberInput.classList.add('invalid');
                          setTimeout(function () {
                            contactNumberError.textContent = '';
                            contactNumberInput.classList.remove('invalid');
                          }, 3000); // Display the error message for 3 seconds
                        } else {
                          contactNumberError.textContent = '';
                          contactNumberInput.classList.remove('invalid');

                          if (barangayValue === '') {
                            barangayError.textContent =
                              'Street/Barangay is required';
                            barangayInput.classList.add('invalid');
                            setTimeout(function () {
                              barangayError.textContent = '';
                              barangayInput.classList.remove('invalid');
                            }, 3000);
                          } else {
                            barangayError.textContent = '';
                            barangayInput.classList.remove('invalid');

                            if (cityValue === '') {
                              cityError.textContent =
                                'City/Municipality is required';
                              cityInput.classList.add('invalid');
                              setTimeout(function () {
                                cityError.textContent = '';
                                cityInput.classList.remove('invalid');
                              }, 3000);
                            } else {
                              cityError.textContent = '';
                              cityInput.classList.remove('invalid');

                              if (provinceValue === '') {
                                provinceError.textContent =
                                  'Province is required';
                                provinceInput.classList.add('invalid');
                                setTimeout(function () {
                                  provinceError.textContent = '';
                                  provinceInput.classList.remove('invalid');
                                }, 3000);
                              } else {
                                provinceError.textContent = '';
                                provinceInput.classList.remove('invalid');

                                if (passwordValue === '') {
                                  passwordError.textContent =
                                    'Password is required';
                                  passwordInput.classList.add('invalid');
                                  setTimeout(function () {
                                    passwordError.textContent = '';
                                    passwordInput.classList.remove('invalid');
                                  }, 3000); // Display the error message for 3 seconds
                                } else if (
                                  passwordValue.length < 8 ||
                                  !/[a-z]/.test(passwordValue) ||
                                  !/[A-Z]/.test(passwordValue) ||
                                  !/[0-9]/.test(passwordValue)
                                ) {
                                  passwordError.textContent =
                                    'Password must be at least 8 characters and contain at least one lowercas/uppercase letter, and number';
                                  passwordInput.classList.add('invalid');
                                  setTimeout(function () {
                                    passwordError.textContent = '';
                                    passwordInput.classList.remove('invalid');
                                  }, 3000); // Display the error message for 3 seconds
                                } else if (passwordValue != re_passwordValue) {
                                  re_passwordError.textContent =
                                    'Password are not match';
                                  re_passwordInput.classList.add('invalid');
                                  setTimeout(function () {
                                    re_passwordError.textContent = '';
                                    re_passwordInput.classList.remove(
                                      'invalid'
                                    );
                                  }, 3000);
                                } else {
                                  // Password is valid, remove any error messages
                                  re_passwordError.textContent = '';
                                  re_passwordInput.classList.remove('invalid');

                                  // If all validations pass, submit the form
                                  // forms.submit();
                                  // If all validations pass, trigger the form submission
                                  const registerInput =
                                    document.createElement('input');
                                  registerInput.setAttribute('type', 'hidden');
                                  registerInput.setAttribute(
                                    'name',
                                    'register'
                                  );
                                  registerInput.setAttribute('value', '1');
                                  forms.appendChild(registerInput);
                                  forms.submit();
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    // }
  }
});

const birthdayDayInput = document.getElementById('birthday-day');

birthdayDayInput.addEventListener('input', function (event) {
  const inputValue = event.target.value;

  // Remove any non-digit characters from the input value
  const sanitizedValue = inputValue.replace(/\D/g, '');

  // Update the input value with the sanitized value
  event.target.value = sanitizedValue;
});

const studentNumberInput = document.getElementById('student-number');

studentNumberInput.addEventListener('input', function (event) {
  const inputValue = event.target.value;

  // Remove any non-digit characters from the input value
  const sanitizedValue = inputValue.replace(/\D/g, '');

  // Update the input value with the sanitized value
  event.target.value = sanitizedValue;
});
const contactNumberInput = document.getElementById('contact-number');

contactNumberInput.addEventListener('input', function (event) {
  const inputValue = event.target.value;

  if (inputValue.length === 1 && !/[0-9+]/.test(inputValue)) {
    // If the first character is neither a plus sign nor a number, clear the input value
    event.target.value = '';
  } else if (inputValue.length > 1 && !/^[+0-9]*$/.test(inputValue)) {
    // If there are characters other than plus sign and numbers, remove them
    event.target.value = inputValue.replace(/[^+0-9]/g, '');
  }
});
