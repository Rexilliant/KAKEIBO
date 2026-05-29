# Kakeibo - The Japanese Art of Household Budgeting 💴

**Kakeibo** (家計簿) is the art of mindful money management. This application helps you apply the _Kakeibo_ method to track every expense, evaluate your spending habits, and achieve your savings goals with discipline.

## 💡 Core Concepts

This application is designed around the four key _Kakeibo_ questions:

1. **How much money do I have?**
2. **How much do I want to save?**
3. **How much have I spent?**
4. **How can I improve?**

## 🛠️ Tech Stack

- **Framework:** Laravel 10/11
- **Language:** PHP 8.2+
- **Frontend:** Tailwind CSS & Vite
- **Database:** MySQL
- **Version Control:** Git

## 📋 Key Features

- **Manual Recording:** Input transactions using specific Kakeibo categories (_Survival, Optional, Culture, Extra_).
- **Monthly Reflection:** End-of-month summaries to evaluate your spending habits.
- **Savings Goals:** Track your savings progress against your set targets.

## 📈 How to Use

1. **Set Your Budget: Input your monthly income at the beginning of the month.**
2. **Record Expenses: Log expenses as they happen (using the manual/mindful method).**
3. **Evaluate: At the end of the month, open the "Reflection" dashboard to review your spending categories.**

## 🏆 Credits

This project was developed with inspiration and support from **Rexilliant.** Thank you for the contributions and creative ideas that helped shape the workflow and development of this application.

## 🤝 Contributions

If you would like to add analysis features or more in-depth statistical visualizations, please fork the repository and submit a pull request.

## 🚀 Installation

Run the following commands in your terminal:

```bash
# 1. Clone the repository
git clone [your-repo-link]
cd kakeibo-app

# 2. Install dependencies
composer install
npm install

# 3. Configure Environment
cp .env.example .env
php artisan key:generate

# 4. Database Migration (Don't forget to set up your DB in .env first)
php artisan migrate

# 5. Run the Application
npm run dev & php artisan serve
```
