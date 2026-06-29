# School Voting System

> A secure and modern web-based voting system designed for school elections, allowing students to vote electronically while providing administrators with tools to manage candidates, elections, and results.

---

## ✨ Features

### 👨‍🎓 Student Features

- Secure student login
- Cast votes electronically
- View candidate profiles
- Vote only once per election
- Real-time voting interface
- Mobile-responsive design

### 🏫 School Officer Features

- Secure administrator login
- Create and manage elections
- Add, edit, and remove candidates
- Manage student accounts
- Monitor ongoing elections
- View real-time vote counts
- Generate election reports
- Publish election results

### 🗳️ Voting Features

- Single vote per student
- Candidate profile display
- Automatic vote counting
- Election scheduling by date
- Election status management (Upcoming, Ongoing, Closed)
- Result generation

### 📊 Dashboard Features

- Total registered voters
- Total candidates
- Total votes cast
- Voter turnout statistics
- Election analytics
- Live results visualization

---

## 🛠️ Built With

### Frontend

- React.js
- HTML5
- CSS3
- JavaScript

### Backend

- Node.js
- Express.js

### Database

- MySQL

---

## 🌐 System Ports

This system uses the following ports during development:

| Service | Port |
|----------|------|
| React Frontend | **3000** |
| Node.js Backend API | **5000** |
| MySQL Database | **3306** |

Example:

```text
Frontend: http://localhost:3000
Backend API: http://localhost:5000
Database: localhost:3306
```

---

## 📂 Project Structure

```text
School-Voting-System/
│
├── client/                 # React Frontend
│   ├── src/
│   ├── public/
│   └── package.json
│
├── server/                 # Backend API
│   ├── routes/
│   ├── controllers/
│   ├── models/
│   ├── config/
│   └── server.js
│
├── database/
│   └── school_voting_system.sql
│
└── README.md
```

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/school-voting-system.git
```

---

## ⚙️ Installation

### Install Frontend Dependencies

Navigate to the client folder:

```bash
cd client
npm install
```

Start React development server:

```bash
npm start
```

The frontend will run on:

```text
http://localhost:3000
```

---

### Install Backend Dependencies

Navigate to the server folder:

```bash
cd server
npm install
```

Start the backend server:

```bash
npm start
```

The backend API will run on:

```text
http://localhost:5000
```

---

## 🗄️ Database Setup

1. Open **phpMyAdmin**.
2. Create a new database named:

```text
school_voting_system
```

3. Import:

```text
school_voting_system.sql
```

4. Configure database credentials in:

```javascript
server/config/db.js
```

Example:

```javascript
const db = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "school_voting_system",
    port: 3306
});
```

---

## 🔐 System Roles

### Student

- Login
- View candidates
- Cast vote
- View election information

### School Officer / Administrator

- Login
- Manage candidates
- Manage students
- Schedule elections
- Monitor voting progress
- Generate reports
- View results

---

## 📅 Election Management

Administrators can:

- Set election start date
- Set election end date
- Open or close voting sessions
- Publish official election results

---

## ⚠️ Note

This project was developed for educational and academic purposes. Additional security measures are recommended before deployment to a production environment.


Feel free to fork, improve, and contribute to this project.
