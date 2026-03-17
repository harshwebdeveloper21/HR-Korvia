// Initialize Chart.js with labels on the right
const ctx = document.getElementById('employeeChart').getContext('2d');

const data = {
    labels: ['2017', '2018', '2019', '2020', '2021'],
    datasets: [{
            label: 'Hiring Actions',
            data: [220, 214, 279, 234, 252],
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
        },
        {
            label: 'Depart Employment',
            data: [223, 165, 208, 201, 176],
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
        },
        {
            label: 'Promotions',
            data: [34, 30, 37, 58, 57],
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
        },
        {
            label: 'Transfers',
            data: [12, 10, 15, 27, 41],
            backgroundColor: 'rgba(255, 206, 86, 0.6)',
        },
        {
            label: 'Demotions',
            data: [7, 4, 3, 2, 2],
            backgroundColor: 'rgba(153, 102, 255, 0.6)',
        }
    ]
};

const employeeChart = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right', // Moves labels to the right
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Populate Leave Table
function fetchLeaveReport() {
    const leaveData = [
        { name: 'John Doe', type: 'Sick Leave', start: '2025-01-10', end: '2025-01-12', status: 'Approved' },
        { name: 'Jane Smith', type: 'Vacation Leave', start: '2025-01-15', end: '2025-01-20', status: 'Pending' },
        { name: 'David Brown', type: 'Casual Leave', start: '2025-01-18', end: '2025-01-18', status: 'Rejected' },
        { name: 'Emily Davis', type: 'Half Day Leave', start: '2025-01-21', end: '2025-01-21', status: 'Approved' },
    ];

    const tableBody = document.getElementById('leave-table-body');
    tableBody.innerHTML = leaveData.map(leave => `
        <tr>
            <td>${leave.name}</td>
            <td>${leave.type}</td>
            <td>${leave.start}</td>
            <td>${leave.end}</td>
            <td>${leave.status}</td>
        </tr>
    `).join('');
}

fetchLeaveReport();
