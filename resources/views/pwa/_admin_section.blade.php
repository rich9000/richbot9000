
<div class="row mb-2">
    <div class="col-md-12 col-lg-12 ">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="adminTabs" role="tablist">
                    <li class="nav-item " role="presentation">
                        <button class="active nav-link" id="admin-overview-tab" data-bs-toggle="tab" data-bs-target="#adminOverview" type="button" role="tab" aria-controls="overview" aria-selected="true">Admin Overview</button>
                    </li>
                    <li class="nav-item " role="presentation">
                        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">Users</button>
                    </li>
                    <li class="nav-item " role="presentation">
                        <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="false">Events</button>
                    </li>

                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="adminTabsContent">
                    <!-- Users Tab -->
                    <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
@include('pwa._admin_users')

                    </div>




<script>

    // Event listener for "More Info" button
    $('#usersTable').on('click', '.more-info-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        loadUserProfile(userId, userName);
    });

    function loadUserProfile(userId, userName) {
        // Check if the tab already exists
        if ($(`#user-tab-${userId}`).length === 0) {
            // Create new tab and content
            $('#adminTabs').append(`
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="user-tab-${userId}" data-bs-toggle="tab" data-bs-target="#user-content-${userId}" type="button" role="tab" aria-controls="user-content-${userId}" aria-selected="false">
                   User Admin: ${userName} <span class="ms-2 close-tab" data-user-id="${userId}">&times;</span>
                </button>
            </li>
        `);

            $('#adminTabsContent').append(`
            <div class="tab-pane fade" id="user-content-${userId}" role="tabpanel" aria-labelledby="user-tab-${userId}">
                <div id="user-profile-${userId}" class="p-3">Loading profile...</div>
            </div>
        `);

            // Load user profile data
            $.ajax({
                url: `/api/users/${userId}`,
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'Accept': 'application/json'
                },
                success: function(user) {
                    const profileHtml = `
                    <h4>Profile of ${user.name}</h4>
                    <p><strong>Email:</strong> ${user.email}</p>
                    <p><strong>Phone:</strong> ${user.phone_number || 'N/A'}</p>
                    <p><strong>Roles:</strong> ${user.roles.map(role => role.name).join(', ')}</p>
                    <p><strong>Address:</strong> ${user.address || 'N/A'}</p>
                    <p><strong>Date of Birth:</strong> ${user.dob || 'N/A'}</p>
                    <p><strong>Additional Info:</strong> ${user.additional_info || 'N/A'}</p>
                `;
                    $(`#user-profile-${userId}`).html(profileHtml);
                },
                error: function(err) {
                    console.error('Error loading user profile:', err);
                    $(`#user-profile-${userId}`).html('<p class="text-danger">Error loading profile.</p>');
                }
            });

            // Activate the new tab
            $(`#user-tab-${userId}`).tab('show');
        } else {
            // If the tab already exists, just show it
            $(`#user-tab-${userId}`).tab('show');
        }
    }

    // Event listener to close tabs
    $('#adminTabs').on('click', '.close-tab', function() {
        const userId = $(this).data('user-id');
        // Remove the tab and content
        $(`#user-tab-${userId}`).closest('li').remove();
        $(`#user-content-${userId}`).remove();

        // Activate the first tab
        $('#admin-overview-tab').tab('show');
    });


</script>
                    <!-- Events Tab -->
                    <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                        <button id="loadEventsButton" class="btn btn-primary mb-3">Load Event Logs</button>
                        <table id="eventLogsTable" class="display table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Event Type</th>
                                <th>Description</th>
                                <th>User</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Data will be populated here by DataTables -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Example Tab -->
                    <div class="tab-pane fade show active" id="adminOverview" role="tabpanel" aria-labelledby="admin-overview-tab">
                        <h4>Admin Overview</h4>
                        <p>This is an example tab. You can add more content here as needed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const adminTabs = document.getElementById('adminTabs');

        adminTabs.addEventListener('shown.bs.tab', function (event) {
            const activatedTab = event.target.id; // The ID of the activated tab
            console.log('Activated tab:', activatedTab);

            // Example: Load data when a specific tab is shown
            if (activatedTab === 'users-tab') {
                // Load users data
                loadUsersDataTables();
            } else if (activatedTab === 'events-tab') {
                // Load events data
                loadEventsDataTables();
            }
        });



        function loadEventsDataTables() {
            console.log('Loading events...');
            // Add your logic to load events data here
            $('#eventLogsTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: `${apiUrl}/eventlogs`,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                        'Accept': 'application/json'
                    }
                },
                columns: [
                    { data: 'event_type', name: 'event_type' },
                    { data: 'description', name: 'description' },
                    { data: 'user.name', name: 'user.name', defaultContent: 'N/A' },
                    { data: 'created_at', name: 'created_at', render: data => new Date(data).toLocaleString() }
                ],
                order: [[3, 'desc']],
                language: {
                    emptyTable: "No event logs available"
                }
            });

        }





        $('#loadEventsButton').on('click', function() {
            loadEventsDataTables();
        });




    });




</script>