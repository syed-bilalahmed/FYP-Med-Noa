// Med-Nova Frontend Logic

const API_URL = 'http://localhost/FYP/backend/index.php?route=api'; // Fixed to use index.php for routing

// Debugging
console.log('Med-Nova App Initialized. API URL:', API_URL);

const api = {
    getRegions: async () => {
        try {
            const response = await fetch(`${API_URL}/regions`);
            return await response.json();
        } catch (error) {
            console.error("Error fetching regions:", error);
            return [];
        }
    },
    getHospitals: async (regionId = '', type = '') => {
        try {
            let url = `${API_URL}/hospitals`;
            const separator = url.includes('?') ? '&' : '?';
            const params = new URLSearchParams();
            if (regionId) params.append('region_id', regionId);
            if (type && type !== 'all') params.append('type', type);
            const queryString = params.toString();
            if (queryString) url += separator + queryString;
            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            console.error("Error fetching hospitals:", error);
            return [];
        }
    },
    getDoctors: async (hospitalId = '') => {
        try {
            let url = `${API_URL}/doctors`;
            const separator = url.includes('?') ? '&' : '?';
            if (hospitalId) url += separator + `hospital_id=${hospitalId}`;
            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            console.error("Error fetching doctors:", error);
            return [];
        }
    },
    getServices: async () => {
        try {
            const response = await fetch(`${API_URL}/services`);
            return await response.json();
        } catch (error) {
            console.error("Error fetching services:", error);
            return [];
        }
    },
    bookAppointment: async (data) => {
        const response = await fetch(`${API_URL}/appointment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return await response.json();
    },
    sendMessage: async (data) => {
        const response = await fetch(`${API_URL}/contact`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return await response.json();
    },
    getHospital: async (identifier) => {
        try {
            let query = '';
            if (String(identifier).includes('@')) {
                query = `email=${identifier}`;
            } else if (!isNaN(identifier)) {
                query = `id=${identifier}`;
            } else {
                query = `slug=${identifier}`;
            }
            let url = `${API_URL}/hospitals`;
            const separator = url.includes('?') ? '&' : '?';
            const response = await fetch(`${url}${separator}${query}`);
            return await response.json();
        } catch (error) {
            console.error("Error fetching hospital:", error);
            return null;
        }
    }
};

$(document).ready(function () {
    console.log('Document ready, initializing Med-Nova app...');
    // --- SHARED HELPER FUNCTIONS ---
    function loadRegions(selectSelector) {
        console.log('Loading regions for selector:', selectSelector);
        console.log('Element found:', $(selectSelector).length);
        return $.getJSON(`${API_URL}/regions`, function (data) {
            console.log('Regions data received:', data);
            const regionSelect = $(selectSelector);
            regionSelect.empty().append(new Option('Select Region', ''));
            data.forEach(r => {
                regionSelect.append(new Option(r.name, r.id));
                console.log('Added region:', r.name, r.id);
            });
            console.log('Regions loaded into select, total options:', regionSelect.find('option').length);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Failed to load regions:', textStatus, errorThrown, jqXHR.responseText);
        });
    }

    async function onRegionChange(regionId, hospSelectSelector, deptSelectSelector, docSelectSelector) {
        console.log('Region changed to:', regionId, 'for selector:', hospSelectSelector);
        const hospSelect = $(hospSelectSelector);
        hospSelect.empty().append(new Option('Select Hospital', '')).prop('disabled', true);
        if (deptSelectSelector) $(deptSelectSelector).empty().append(new Option('Select Hospital First', '')).prop('disabled', true);
        if (docSelectSelector) $(docSelectSelector).empty().append(new Option('Select Department First', '')).prop('disabled', true);

        if (regionId) {
            console.log('Loading hospitals for region:', regionId);
            hospSelect.prop('disabled', false).append(new Option('Loading...', ''));
            try {
                const hospitals = await $.getJSON(`${API_URL}/hospitals&region_id=${regionId}`);
                console.log('Hospitals loaded:', hospitals);
                hospSelect.empty().append(new Option('Select Hospital', ''));
                hospitals.forEach(h => {
                    hospSelect.append(new Option(h.name, h.id));
                });
            } catch (e) {
                console.error("Error loading hospitals:", e);
                hospSelect.empty().append(new Option('Error loading hospitals', ''));
            }
        }
    }

    async function onHospitalChange(hospitalId, deptSelectSelector, docSelectSelector) {
        console.log('Hospital changed to:', hospitalId, 'for selector:', deptSelectSelector);
        if (deptSelectSelector) {
            const deptSelect = $(deptSelectSelector);
            deptSelect.empty().append(new Option('Select Department', '')).prop('disabled', true);
            if (docSelectSelector) $(docSelectSelector).empty().append(new Option('Select Department First', '')).prop('disabled', true);

            if (hospitalId) {
                console.log('Loading departments for hospital:', hospitalId);
                deptSelect.prop('disabled', false).append(new Option('Loading...', ''));
                try {
                    const departments = await $.getJSON(`${API_URL}/departments&hospital_id=${hospitalId}`);
                    console.log('Departments loaded:', departments);
                    deptSelect.empty().append(new Option('Select Department', ''));
                    departments.forEach(d => {
                        deptSelect.append(new Option(d.name, d.id));
                    });
                } catch (e) {
                    console.error('Error loading departments:', e);
                    deptSelect.empty().append(new Option('Error loading departments', ''));
                }
            }
        }
    }

    async function onDepartmentChange(deptId, docSelectSelector) {
        console.log('Department changed to:', deptId, 'for selector:', docSelectSelector);
        if (docSelectSelector) {
            const docSelect = $(docSelectSelector);
            docSelect.empty().append(new Option('Select Doctor', '')).prop('disabled', true);

            if (deptId) {
                console.log('Loading doctors for department:', deptId);
                docSelect.prop('disabled', false).append(new Option('Loading...', ''));
                try {
                    const hospitalId = $(docSelectSelector).closest('form').find('select[name="hospital"]').val();
                    console.log('Hospital ID for doctors:', hospitalId);
                    const doctors = await $.getJSON(`${API_URL}/doctors&hospital_id=${hospitalId}`);
                    console.log('Doctors loaded:', doctors);
                    const deptName = $(docSelectSelector).closest('form').find('select[name="department"] option:selected').text();
                    console.log('Filtering by department name:', deptName);
                    const filtered = doctors.filter(d => d.specialty === deptName || d.specialization === deptName);
                    console.log('Filtered doctors:', filtered);

                    docSelect.empty().append(new Option('Select Doctor', ''));
                    if (filtered.length > 0) {
                        filtered.forEach(d => {
                            docSelect.append(new Option(d.name, d.id));
                        });
                    } else {
                        docSelect.append(new Option(`No doctors in ${deptName}`, ''));
                    }
                } catch (e) {
                    console.error('Error loading doctors:', e);
                    docSelect.empty().append(new Option('Error loading doctors', ''));
                }
            }
        }
    }

    // --- HOME PAGE LOGIC ---
    if ($('#home-doctors-grid').length) {
        api.getDoctors().then(doctors => {
            const topDoctors = doctors.slice(0, 3);
            const doctorsHtml = topDoctors.map(doc => `
                <div class="col-md-4">
                    <div class="doctor-pro-card">
                        <div class="doctor-img-box">
                            <img src="${doc.image || 'assets/default-doctor.jpg'}" alt="${doc.name}" onerror="this.src='https://placehold.co/400x400/eef6ff/2f7bff?text=Dr'">
                        </div>
                        <h4 class="doctor-name">${doc.name}</h4>
                        <div class="doctor-role">${doc.specialty}</div>
                    </div>
                </div>
            `).join('');
            $('#home-doctors-grid').html(doctorsHtml);
        });
    }

    // --- DOCTORS/FACILITIES PAGE LOGIC ---
    if ($('#main-list-container').length) {
        let allFacilities = [];
        let currentType = 'hospital';
        let currentRegion = '';

        api.getRegions().then(regions => {
            const regionSelect = $('#region-filter');
            regions.forEach(r => {
                regionSelect.append(new Option(r.name, r.id));
            });
        });

        const loadFacilities = async () => {
            $('#main-list-container').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');
            allFacilities = await api.getHospitals(currentRegion, currentType);
            renderFacilities(allFacilities);
        };

        const renderFacilities = (facilities) => {
            const searchQuery = $('#universal-search').val().toLowerCase();
            const filtered = facilities.filter(f => {
                return f.name.toLowerCase().includes(searchQuery) || (f.address || '').toLowerCase().includes(searchQuery);
            });
            $('#results-count').text(`${filtered.length} Facilities Found`);
            const html = filtered.map(f => `
                <div class="facility-card p-3 rounded-3 bg-white mb-2 shadow-sm border-start border-4 border-primary" data-id="${f.id}" onclick="showFacilityDetails(${f.id})">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 50px; height: 50px;">
                            ${f.name.charAt(0)}
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">${f.name}</h6>
                            <div class="small text-muted"><i class="fas fa-map-marker-alt"></i> ${f.address || 'Kohat'}</div>
                        </div>
                    </div>
                </div>
            `).join('');
            $('#main-list-container').html(html);
        };

        window.showFacilityDetails = async (id) => {
            const facility = allFacilities.find(f => f.id == id);
            if (!facility) return;
            $('#detail-panel').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');
            const doctors = await api.getDoctors(id);
            const doctorsHtml = doctors.length > 0 ? doctors.map(d => `
                <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded hover-bg-light">
                    <div class="flex-grow-1">
                        <div class="fw-bold small">${d.name}</div>
                        <div class="text-xs text-muted">${d.specialty}</div>
                    </div>
                    <a href="appointment.html?doctor=${encodeURIComponent(d.name)}&hospital=${encodeURIComponent(facility.name)}" class="btn btn-sm btn-primary rounded-pill px-3">Book</a>
                </div>
            `).join('') : '<div class="text-center text-muted small py-3">No doctors listed yet.</div>';

            $('#detail-panel').html(`
                <div class="bg-white p-4 rounded-4 shadow">
                    <h4 class="fw-bold mb-1">${facility.name}</h4>
                    <p class="text-muted small border-bottom pb-3">${facility.address || 'Kohat'}</p>
                    <h6 class="fw-bold text-muted small mb-3">Available Doctors</h6>
                    <div style="max-height: 400px; overflow-y: auto;">${doctorsHtml}</div>
                </div>
            `).hide().fadeIn(300);
        };

        $('#region-filter').change(function () { currentRegion = $(this).val(); loadFacilities(); });
        $('#facility-tabs button').click(function () {
            $('#facility-tabs button').removeClass('active');
            $(this).addClass('active');
            currentType = $(this).data('type');
            loadFacilities();
        });
        $('#universal-search').on('input', function () { renderFacilities(allFacilities); });
        loadFacilities();
    }

    // --- APPOINTMENT FORM LOGIC ---
    if ($('#step-2-form').length) {
        console.log('Appointment form found, loading regions...');
        loadRegions('#step-2-form select[name="region"]').then(async () => {
            console.log('Regions loaded successfully');
            const urlParams = new URLSearchParams(window.location.search);
            const hospitalParam = urlParams.get('hospital_id') || urlParams.get('hospital');
            if (hospitalParam) {
                console.log('Hospital param found:', hospitalParam);
                const hospital = await api.getHospital(hospitalParam);
                if (hospital && hospital.id) {
                    $('#step-2-form select[name="region"]').val(hospital.region_id);
                    await onRegionChange(hospital.region_id, '#step-2-form select[name="hospital"]', '#step-2-form select[name="department"]', '#step-2-form select[name="doctor"]');
                    $('#step-2-form select[name="hospital"]').val(hospital.id);
                    await onHospitalChange(hospital.id, '#step-2-form select[name="department"]', '#step-2-form select[name="doctor"]');

                    const deptParam = urlParams.get('dept') || urlParams.get('department');
                    if (deptParam) {
                        const deptOption = $('#step-2-form select[name="department"] option').filter(function () {
                            return $(this).text().toLowerCase() === deptParam.toLowerCase();
                        });
                        if (deptOption.length) {
                            $('#step-2-form select[name="department"]').val(deptOption.val()).trigger('change');
                        }
                    }
                }
            }
        }).catch(error => {
            console.error('Error loading regions:', error);
        });

        $('#step-2-form select[name="region"]').change(function () {
            onRegionChange($(this).val(), '#step-2-form select[name="hospital"]', '#step-2-form select[name="department"]', '#step-2-form select[name="doctor"]');
        });
        $('#step-2-form select[name="hospital"]').change(function () {
            onHospitalChange($(this).val(), '#step-2-form select[name="department"]', '#step-2-form select[name="doctor"]');
        });
        $('#step-2-form select[name="department"]').change(function () {
            onDepartmentChange($(this).val(), '#step-2-form select[name="doctor"]');
        });

        $('#step-1-form').submit(function (e) {
            e.preventDefault();
            $('#step-1').addClass('d-none');
            $('#step-2').removeClass('d-none');
            $('#appointment-progress').css('width', '66%');
            $('#progress-text').text('Step 2: Selection');
        });

        $('#back-to-step-1').click(function () {
            $('#step-2').addClass('d-none');
            $('#step-1').removeClass('d-none');
            $('#appointment-progress').css('width', '33%');
            $('#progress-text').text('Step 1: Info');
        });

        $('#step-2-form').submit(async function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Booking...');
            const form = $(this);
            const payload = {
                name: $('#name').val(),
                phone: $('#phone').val(),
                date: form.find('input[name="date"]').val(),
                time: form.find('select[name="time"]').val(),
                hospitalId: form.find('select[name="hospital"]').val(),
                doctorId: form.find('select[name="doctor"]').val(),
                type: 'consultation'
            };
            console.log('Submitting appointment with payload:', payload);
            try {
                const res = await api.bookAppointment(payload);
                if (res.success) {
                    $('#step-2').addClass('d-none');
                    $('#step-3').removeClass('d-none');
                    $('#appointment-progress').css('width', '100%');
                    $('#progress-text').text('Step 3: Done');
                    $('#confirm-name').text(payload.name);
                    $('#confirm-date').text(payload.date);
                } else {
                    alert(res.message || 'Booking failed');
                    btn.prop('disabled', false).text('Next Step ->');
                }
            } catch (e) {
                alert('Connection error.');
                btn.prop('disabled', false).text('Next Step ->');
            }
        });
    }

    // --- DIET PLAN FORM LOGIC ---
    if ($('#diet-plan-form').length) {
        loadRegions('#diet-plan-form select[name="region"]');
        $('#diet-plan-form select[name="region"]').change(function () {
            onRegionChange($(this).val(), '#diet-plan-form select[name="hospital"]', '#diet-plan-form select[name="department"]', '#diet-plan-form select[name="doctor"]');
        });
        $('#diet-plan-form select[name="hospital"]').change(function () {
            onHospitalChange($(this).val(), '#diet-plan-form select[name="department"]', '#diet-plan-form select[name="doctor"]');
        });
        $('#diet-plan-form select[name="department"]').change(function () {
            onDepartmentChange($(this).val(), '#diet-plan-form select[name="doctor"]');
        });
        $('#diet-plan-form').submit(async function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const originalText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
            const formData = {
                name: $(this).find('input[name="name"]').val(),
                phone: $(this).find('input[name="phone"]').val(),
                age: $(this).find('input[name="age"]').val(),
                weight: $(this).find('input[name="weight"]').val(),
                height: $(this).find('input[name="height"]').val(),
                hospitalId: $(this).find('select[name="hospital"]').val(),
                doctorId: $(this).find('select[name="doctor"]').val(),
                goal: $(this).find('select[name="goal"]').val(),
                conditions: $(this).find('textarea[name="conditions"]').val()
            };
            try {
                const res = await $.ajax({
                    url: `${API_URL}/dietPlan`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData)
                });
                if (res.success) {
                    $('#diet-plan-form').addClass('d-none');
                    $('#diet-success').removeClass('d-none');
                } else {
                    alert(res.message || 'Failed');
                    btn.html(originalText).prop('disabled', false);
                }
            } catch (err) {
                alert('Connection Error');
                btn.html(originalText).prop('disabled', false);
            }
        });
    }

    // --- BLOOD DONATION FORM LOGIC ---
    if ($('#donor-form').length) {
        loadRegions('#donor-form select[name="region"]');
        $('#donor-form select[name="region"]').change(function () {
            onRegionChange($(this).val(), '#donor-form select[name="hospital"]');
        });
        $('#donor-form').submit(async function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.text('Registering...').prop('disabled', true);
            const formData = {
                name: $(this).find('input[name="name"]').val(),
                age: $(this).find('input[name="age"]').val(),
                blood_group: $(this).find('select[name="blood_group"]').val(),
                phone: $(this).find('input[name="phone"]').val(),
                location: $(this).find('input[name="location"]').val(),
                hospitalId: $(this).find('select[name="hospital"]').val()
            };
            try {
                const res = await $.ajax({
                    url: `${API_URL}/registerDonor`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData)
                });
                if (res.success) {
                    alert('Registered as donor!');
                    this.reset();
                } else {
                    alert('Error: ' + res.message);
                }
            } catch (e) { alert('Connection error'); }
            finally { btn.text('Register Now').prop('disabled', false); }
        });
    }

    if ($('#patient-form').length) {
        loadRegions('#patient-form select[name="region"]');
        $('#patient-form select[name="region"]').change(function () {
            onRegionChange($(this).val(), '#patient-form select[name="hospital"]');
        });
        $('#patient-form').submit(async function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.text('Broadcasting...').prop('disabled', true);
            const formData = {
                patient_name: $(this).find('input[name="patient_name"]').val(),
                blood_group: $(this).find('select[name="blood_group"]').val(),
                urgency: $(this).find('select[name="urgency"]').val(),
                phone: $(this).find('input[name="phone"]').val(),
                hospitalId: $(this).find('select[name="hospital"]').val()
            };
            try {
                const res = await $.ajax({
                    url: `${API_URL}/requestBlood`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData)
                });
                if (res.success) {
                    alert('Broadcasted successfully!');
                    this.reset();
                } else {
                    alert('Error: ' + res.message);
                }
            } catch (e) { alert('Connection error'); }
            finally { btn.text('Broadcast Request').prop('disabled', false); }
        });
    }

    // --- CONTACT FORM LOGIC ---
    $('#contact-form-submit').submit(async function (e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).text('Sending...');
        const formData = {
            name: $(this).find('input[name="name"]').val(),
            phone: $(this).find('input[name="phone"]').val(),
            message: $(this).find('textarea[name="message"]').val()
        };
        try {
            const res = await api.sendMessage(formData);
            if (res.success) {
                alert('Message sent!');
                this.reset();
            } else { alert('Error sending message'); }
        } catch (e) { alert('Connection error'); }
        finally { btn.prop('disabled', false).text('Send Message'); }
    });

    // --- APPOINTMENT FORM LOGIC ---
    if ($('#appointment-form').length) {
        // Load doctors for appointment form
        api.getDoctors().then(doctors => {
            const doctorSelect = $('#appointment-form select[name="doctorId"]');
            doctorSelect.empty().append('<option selected disabled>Choose Doctor</option>');
            doctors.forEach(doctor => {
                doctorSelect.append(`<option value="${doctor.id}">${doctor.name} - ${doctor.specialty}</option>`);
            });
        }).catch(error => {
            console.error('Error loading doctors:', error);
        });

        $('#appointment-form').submit(async function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Booking...');
            const formData = {
                name: $(this).find('input[name="name"]').val(),
                phone: $(this).find('input[name="phone"]').val(),
                date: $(this).find('input[name="date"]').val(),
                doctorId: $(this).find('select[name="doctorId"]').val(),
                healthType: $(this).find('select[name="healthType"]').val()
            };
            try {
                const res = await api.bookAppointment(formData);
                if (res.success) {
                    alert('Appointment booked successfully!');
                    this.reset();
                } else { alert('Error booking appointment: ' + res.message); }
            } catch (e) { alert('Connection error'); }
            finally { btn.prop('disabled', false).text('Appointment Now'); }
        });
    }

    // --- NEWSLETTER LOGIC ---
    $('.newsletter-input').submit(function (e) {
        e.preventDefault();
        const phone = $(this).find('input[type="tel"]').val();
        alert(`We'll contact you on WhatsApp at ${phone}!`);
        this.reset();
    });

    // --- HOME PAGE DOCTORS GRID ---
    if ($('#home-doctors-grid').length) {
        api.getDoctors().then(doctors => {
            const grid = $('#home-doctors-grid');
            grid.empty();

            if (doctors.length === 0) {
                grid.html('<div class="col-12 text-center py-5"><p>No doctors available at the moment.</p></div>');
                return;
            }

            // Show only first 3 doctors
            doctors.slice(0, 3).forEach(doctor => {
                const doctorCard = `
                    <div class="col-md-6 col-lg-4">
                        <div class="doctor-card-pro text-center">
                            <div class="doctor-img-wrapper">
                                <img src="${doctor.image || 'https://placehold.co/300x300/eef6ff/2f7bff?text=Doctor'}" 
                                     alt="${doctor.name}" class="doctor-img">
                            </div>
                            <div class="doctor-info">
                                <h5 class="doctor-name">${doctor.name}</h5>
                                <p class="doctor-specialty text-primary">${doctor.specialty}</p>
                                <div class="doctor-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    <span>${doctor.rating || '4.5'}</span>
                                </div>
                                <a href="doctors.html" class="btn btn-outline-primary btn-sm mt-2">View Profile</a>
                            </div>
                        </div>
                    </div>
                `;
                grid.append(doctorCard);
            });
        }).catch(error => {
            console.error('Error loading doctors:', error);
            $('#home-doctors-grid').html('<div class="col-12 text-center py-5"><p>Error loading doctors.</p></div>');
        });
    }

    // Mobile Menu Toggle
    $('#mobile-menu-btn').click(function () {
        $('#mobile-menu').toggle();
    });
});
