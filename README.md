# Framework Project Exam

| Course     | Framework Project 1                  |
|------------|--------------------------------------|
| Code       | CU75080V3                            |
| Date       | MOCK EXAM                            |
| Time       | MOCK EXAM                            |
| Duration   | 180 minutes (+extra)                 |
| Submission | Submit on Athena (exam.hboict.hz.nl) |

**In case of any discrepancy between the English and Dutch versions of the instructions, the English version shall be considered the definitive and authoritative source.**

### Instructions
 - Read the requirements for the solution, starting on the following page.
 - Download and load the starter environment from Athena.
 - Create your implementation for the requirements.
 - Start the development server by running `php maestro serve` in the terminal.
   - You can access the application at `http://localhost:8888` in your browser.

### Allowed
 - You may only use your own assigned computer.
 - On your computer, you may only use your PHPStorm for development and your browser for testing.
 - You may only access Athena (assessment submission portal).
 - You may only use the starter code and assets given.

### NOT Allowed
 - You may not make use of any physical or digital resources (including books, notes, code snippets, previous assessments or assignments) locally or remotely stored, Internet resources (for reference, communication, creation, and/or editing), communication devices (including mobile phones, smartwatches, and headphones), human assistance (verbal, written, and/or gestures), and/or any form of artificial intelligence or automated assistance (including, but not limited to Copilot and ChatGPT). Any uncertainty should be clarified with the examiner or invigilator before starting.
 - You may not provide or attempt to provide any assistance (verbal, written, and/or gestures) to anyone.

### Submission
 - You must submit to Athena.
 - Create a single .zip file that **only** contains your `app` and `database` directories.
 - Submit the .zip-file to Athena.
 - You are allowed to hand in multiple times during the exam.
 - The last code you handed in will be considered your final submission and will be graded.

<div class="page"></div>

# Custodire

## System Context
The Suppuratio have taken total private curatorship of all Earth's governments. Which started as an assembly of the social media companies of yesteryear, The Supperatio have since devolved into an obstinate megacorporation. Many saw it coming, some cheered it on, and eventually government greed yielded ultimate control to The Suppuratio. They have since seized control of critical infrastructure, production, as well as law and order by means of their "Trust and Safety Committee", also known as The Venalis.

In order to maintain their control, The Suppuratio has implemented a system of "Warrants". Warrants are drafted against individuals who are accused of crimes or violations against society's terms and conditions. Anyone is able to make a claim against another individual, and if the claim is deemed valid by The Venalis, a Warrant is issued against the accused.

Warrants are executed by Warrant Agents, who are managed by Warrant Brokers. The Warrant Agents are responsible for carrying out the Warrants, which can range from detainment to elimination. The Warrant Brokers are responsible for managing the Agents and bidding on the Warrants.

### Business Process
A Claimant makes an allegation at a Trust and Safety Centre. A warrant is then drafted against the accused, the allegation goes to The Venalis who will determine the validity of the claim. If they find that the claim is valid, they can issue the Warrant. There are three levels of Warrants: 
 1. Detainment,
 2. Nonaccidental trauma,
 3. and Immediate Elimination.

Each Warrant Agent is assigned to a Broker who will bid on the warrants the Agents must execute. Each Warrant Agent has a specific level, and Warrants can only be assigned to Agents with the same level or higher.

The stages of a Warrant are as follows:
 1. **Draft:** A claim has been made and a warrant has been drafted, but it has not been issued yet. 
 2. **Issued:** Warrant has been assigned a level by The Venalis and it is now available for bidding by Warrant Agents.
 3. **Bidding:** Once a single bid has been placed on the Warrant, it enters the bidding stage.
 4. **Assigned:** The Warrant has been assigned to a Warrant Agent. Bidding is closed.
 5. **Completed:** The Warrant has been completed by the assigned Agent.
 6. **Cancelled:** The Warrant has been cancelled by The Venalis. No further action can be taken on the Warrant.

When a Warrant has been issued, all Warrant Brokers can then start bidding on the Warrant. Brokers bid on behalf of the Agents they manage. They can bid one or more of their Warrant Agents as well as the fee at which the Agent will complete the Warrant.

After bidding, The Venalis will assign the Warrant to the Agent. It does not necessarily have to be the Agent with the highest bid. The Warrant is then marked as "assigned". Once the Agent has completed the Warrant, The Venalis will mark it as "completed". The Venalis can also cancel a Warrant at any time, which will mark it as "cancelled" and no further action can be taken on it.

## Instructions
Build a web application using the Maestro framework that implements the following functional and non-functional requirements for the Custodire system. A partial implementation of the system is provided. You must design an appropriate data model and implement the missing functionality.

### Functional Requirements
- Brokers should be managed, including creation, editing, and deletion of brokers. Each broker has a name. 

- When viewing a broker, a list of all their agents should be displayed as well.

- Agents should be managed, including creation, editing, and deletion of agents. Each agent has a name, a status (active/inactive), and a level (1-3), as well as an assigned broker.

- Claimants should be able to Request draft warrants. Draft warrants contain the accused's name and a description of the alleged violation. Only one draft warrant can be made against an accused at a time.

- A list of all warrants should be displayed in a table format. It should include the accused's name, the allegation and the stage of the warrant. The row of each warrant should be colour coded based on the level of the warrant:
  - Level 1: Green
  - Level 2: Orange
  - Level 3: Red

- Viewing the warrant reveals more details, including the level of the warrant, the assigned agent and the winning bid amount (if any).

- Editing a warrant allows for the level of the warrant to be assigned as well as the stage of the warrant to be updated. The stage of the warrant can only be updated to "issued", "completed", or "cancelled" manually. All other phases are updated automatically.

- To bid on a warrant, from the agent list, a list of all issued warrants that meets the agent's level is displayed. The broker may then enter a bid amount for each warrant. The stage of the warrant is updated to "bidding" once a bid has been placed on it.

- Once bids have been made, an agent can be assigned to the warrant. When editing the warrant, a list of all agents that have an active bid on the warrant is displayed. An agent can then be assign to the warrant, which will update the stage of the warrant to "assigned".

- Warrants cannot be deleted. Should the agent assigned to the warrant be deleted, the warrant must be unassigned.

- The front page should display a dashboard with the following information:
  - Total number of warrants, brokers, and agents
  - Subtotals of warrants in the following stage categories:
    - Started: draft, issued, bidding
    - In Progress: assigned
    - Closed: completed, cancelled

### Non Functional Requirements
- PHP code style must be consistent with PSR-12.

- Static code analysis must be performed at PHPStan level 8. 

- The web application must be designed and implemented following established software engineering principles, including proper database design.

- Data manipulation must be done using the repository pattern.

- The functional design of the application should be consistent with the provided wireflow.

- The user interface should be clean and intuitive, with clear navigation and consistent styling, providing validation and error handling for user input.

## Sample Data
Below is a sample dataset that illustrates the requirements of the system. An Excel file containing a more expanded version of this dataset is provided in the `docs` directory. You can use this dataset to test your implementation, but you are not required to use this exact data.

> This is only sample data to illustrate the requirements. You do not need to use this exact data, but your implementation should be able to support similar data.

| Broker        | Agent      | Active | Level | Name 1       | Allegation 1                                | Level 1 | Stage 1   | Bid 1 | Name 2       | Allegation 2                                | Level 2 | Stage 2   | Bid 2 |
|---------------|------------|--------|-------|--------------|---------------------------------------------|-----------------|-----------|--------------|--------------|---------------------------------------------|-----------------|-----------|--------------|
| Father Caelum | Jax Calder | TRUE   | 3     | Aaron Blight | Played unauthorised music                   | 2               | Assigned  | 19900        | The Tinman   | Hurt claimant's feelings                    | 3               | Completed | 38000        |
|               | Rowan Pike | TRUE   | 3     | Liesel Korr  | Did not follow required penance for words   | 2               | Assigned  | 13800        | Marcus Yew   | Did not adhere to minimum daily screen time | 2               | Bidding   | 15000        |
| Rodere Rex    | Tomás Redd | TRUE   | 2     | Petra Lin    | Used an ad-blocker                          | 1               | Completed | 26500        |              |                                             |                 |           |              |
|               | Briar Holt | TRUE   | 2     | Marcus Yew   | Did not adhere to minimum daily screen time | 2               | Bidding   | 22700        | Samuel Oakes | Used a large language model                 | 1               | Completed | 11600        |

<div class="page"></div>

### Wireflow
The wireflow below illustrates the expected flow of the application. It only shows the **primary flows**, and not alternative or exceptional flows.

> This wireflow is provided as a guide to illustrate the expected flow of the application. You are not required design the application exactly as shown in the wireflow, but your implementation should be consistent with the flow illustrated in the wireflow.

![Wireflow](./docs/wireflow.png)

<div class="page"></div>

# Grading

A maximum of **100 points** can be awarded. A provisional pass mark of **60 points** will be applied.

## Assessibility Criteria
PHP must execute without any Fatal errors.

## Grading Criteria

### Code Quality & Style [20]
 - Code Quality is tested using PHPCS adhering to PSR-12 coding standards, and PHPStan adhering to level 8 rules.
 - Each violation found results in a deduction of points, up to a maximum of 20 points.
   - Each PHPCS violation deducts 0.5 points.
   - Each PHPStan violation deducts 1 point.
 - The code quality grade may be overridden by the assessor.

> **HINT**: You can run `php maestro phpcs` to check for PHPCS issues, and `php maestro phpstan` to check for PHPStan issues.

### Data Storage [30]
 - Database Design: Tables should be correctly normalised with appropriate datatypes, proper relationships, and sensible constraints.
 - Repository Pattern: Repositories are the single point of data access, ensuring clear separation of concerns with clear methods.
 - SQL Queries: Queries should be correct, efficient, and secure, using prepared statements and avoiding unnecessary complexity or duplication.

### Application Logic [40]
 - Requirements & Correctness: Required features are fully implemented and behaving correctly according to functional requirements, including proper handling of edge cases and invalid input.
 - Controllers & Separation of Concerns: Controllers focused on request handling, data access to appropriate layers.
 - Architecture & Design Patterns: Following the intended architecture and design patterns promoting maintainability, extensibility, and low coupling.

### User Experience [10]
 - Wireflow & UI Cohesion: The user interface should follow the provided wireflow, with consistent layout, navigation, and interaction patterns across the application.
 - Validation & Error Handling: User input should be clearly validated with meaningful feedback.

### Architecture Penalty [-20]
If the MVC architecture with proper separation of concerns, including repository pattern, is not followed, a penalty of up to 20 points may be applied.

> **HINT**: You can run `php maestro deptrac` to check for architecture issues.

# Credits
 - Theme: vapor (https://bootswatch.com)
 - Fonts: Orbitron (https://fonts.google.com/specimen/Orbitron)