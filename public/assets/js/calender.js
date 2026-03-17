// document.addEventListener('DOMContentLoaded', function () {
//     const calendarEl = document.getElementById('calendar');
    
//     const calendar = new FullCalendar.Calendar(calendarEl, {
//         initialView: 'dayGridMonth',
//         headerToolbar: {
//             left: 'prev,next today',
//             center: 'title',
//             right: 'dayGridMonth,timeGridWeek,timeGridDay'
//         },
//         locale: 'en',
//         buttonText: {
//             today: 'Today',
//             day: 'Day',
//             week: 'Week',
//             month: 'Month'
//         },
//         events: [
//             {
//                 title: 'John Doe',
//                 start: '2025-01-15',
//                 extendedProps: { 
//                     user: 'User 1',
//                     leaveType: 'Vacation'
//                 },
//                 classNames: ['vacation-leave'],
//                 description: '🏖️ Vacation Leave'
//             },
//             {
//                 title: 'Jane Smith',
//                 start: '2025-01-20',
//                 extendedProps: { 
//                     user: 'User 2', 
//                     leaveType: 'Sick Leave' 
//                 },
//                 classNames: ['sick-leave'],
//                 description: '🤒 Sick Leave'
//             },
//             {
//                 title: 'Mike Johnson',
//                 start: '2025-02-01',
//                 extendedProps: { 
//                     user: 'User 3',
//                     leaveType: 'Paid Leave' 
//                 },
//                 classNames: ['paid-leave'],
//                 description: '💵 Paid Leave'
//             },
//             {
//                 title: 'Susan Lee',
//                 start: '2025-02-10',
//                 extendedProps: { 
//                     user: 'User 4', 
//                     leaveType: 'Casual Leave' 
//                 },
//                 classNames: ['casual-leave'],
//                 description: '🧑‍💻 Casual Leave'
//             }
//         ]
//     });

//     calendar.render();

//     // Apply filters
//     document.getElementById('user-filter').addEventListener('change', applyFilters);
//     document.getElementById('leave-type-filter').addEventListener('change', applyFilters);
//     document.getElementById('start-date-filter').addEventListener('change', applyFilters);
//     document.getElementById('end-date-filter').addEventListener('change', applyFilters);

//     function applyFilters() {
//         const selectedUser = document.getElementById('user-filter').value;
//         const selectedLeaveType = document.getElementById('leave-type-filter').value;
//         const startDate = document.getElementById('start-date-filter').value;
//         const endDate = document.getElementById('end-date-filter').value;
        
//         let filteredEvents = calendar.getEvents();

//         // Filter by User
//         if (selectedUser) {
//             filteredEvents = filteredEvents.filter(event => event.extendedProps.user === selectedUser);
//         }

//         // Filter by Leave Type
//         if (selectedLeaveType) {
//             filteredEvents = filteredEvents.filter(event => event.extendedProps.leaveType === selectedLeaveType);
//         }

//         // Filter by Date Range
//         if (startDate) {
//             filteredEvents = filteredEvents.filter(event => event.start >= startDate);
//         }
//         if (endDate) {
//             filteredEvents = filteredEvents.filter(event => event.end <= endDate);
//         }

//         // Remove all events and add the filtered ones
//         calendar.getEvents().forEach(event => event.remove());
//         calendar.addEventSource(filteredEvents);
//     }
// });
