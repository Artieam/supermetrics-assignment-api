# supermetrics-assignment-api
Fetch and manipulate JSON data from a fictional Supermetrics Social Network REST API.

<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

It is a test task application, that fetches and manipulates JSON data from a fictional Supermetrics Social Network REST API.

### Built With

* [PHP 7.4.13](https://www.php.net/downloads.php#v7.4.13)



<!-- GETTING STARTED -->
## Getting Started

To run this application locally do steps.

### Prerequisites

For more convenient development, install Docker Desktop.
* [Docker](https://www.docker.com/get-started)

### Installation

1. Clone the repo
   ```bash
   git clone https://github.com/Artieam/supermetrics-assignment-api.git
   ```
2. Copy .env.example to .env
   ```bash
   cp .env.example .env
   ```
3. Enter your API credentials in `.env`
   ```bash
   SUPERMETRICS_URL=https://api.supermetrics.com/assignment/
   SUPERMETRICS_API_CLIENT_ID=
   SUPERMETRICS_API_EMAIL=your@email.address
   SUPERMETRICS_API_NAME=Your_Name
   ```
4. Run docker-compose.yaml
   ```bash
   docker-compose up -d 
   ```


<!-- USAGE EXAMPLES -->
## Usage
This app parses data from API and show stats on the following:
1. Average character length of posts per month
2. Longest post by character length per month
3. Total posts split by week number
4. Average number of posts per user per month

You can use this API application two ways to grab parsed data from Supermetrics test posts:
1. Console run to show all types of stats
   ```bash
   docker-compose exec app php app/Console/PostParserCommand.php
   ```
2. Via http requests
- 1 statistics
   ```bash
   http://localhost/index.php?method=avrPostLenMonth
   ```
- 2 statistics
   ```bash
   http://localhost/index.php?method=longestPostMonth
   ```
- 3 statistics
   ```bash
   http://localhost/index.php?method=totalPostWeekly
   ```
- 4 statistics.

  Here you can fetch average number of posts per user per month
   ```bash
   http://localhost/index.php?method=avrPostNumUserMonth
   ```
  
  Or average number of posts per month per user 
   ```bash
   http://localhost/index.php?method=avrPostNumMonthUser
   ```  

<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.


<!-- CONTACT -->
## Contact

Artem Sorokin - arteam91@gmail.com

Project Link: [https://github.com/Artieam/supermetrics-assignment-api](https://github.com/Artieam/supermetrics-assignment-api)
