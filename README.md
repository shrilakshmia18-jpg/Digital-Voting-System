# Digital-Voting-System
# 🗳️ Digital Voting System - Secure E-Voting Platform

A secure and transparent web-based Digital Voting System built using PHP and MySQL. Designed to conduct college or small-scale elections digitally with fraud prevention and one-voter-one-vote policy.

### ✨ Features
**Admin Panel:**
- Add / Remove Voters and Candidates
- Create and Manage Elections
- Upload Candidate Photo & Symbol
- Live Vote Counting
- Publish Final Results
- Secure Admin Login

**Voter Panel:**
- Secure Voter Login with Voter ID
- View Candidate List
- Vote Once Only (Prevents Double Voting)
- Vote Confirmation Page
- Auto-Logout After Voting

**Security Features:**
- One Voter = One Vote Logic
- Session-Based Authentication
- Password Hashing
- Prevents Multiple Voting Attempts

### 🛠️ Tech Stack
- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Backend:** PHP
- **Database:** MySQL
- **Server:** XAMPP / Apache

### 💾 Database Structure
- `admin` - Admin login credentials
- `voters` - Voter details and voting status (has_voted)
- `candidates` - Candidate name, party, photo
- `votes` - Stores voter_id and candidate_id mapping

### 🚀 How to Run
1. Clone the repo: `git clone https://github.com/shrilakshmia18-jpg/DIGITAL-VOTING-SYSTEM.git`
2. Import `voting_db.sql` to phpMyAdmin
3. Move project to `htdocs` folder in XAMPP
4. Start Apache & MySQL
5. Open `localhost/DIGITAL-VOTING-SYSTEM`

Default Logins:
- Admin: admin / admin123
- Voter: voter_id from database

### 👩‍💻 Developed By
Shrilakshmi A

---
⭐ Star this repo if you found it useful!
