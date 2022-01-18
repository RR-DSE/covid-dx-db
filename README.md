# covid-dx-db

This personal repository contains source code for a platform that I created to help a Portuguese health institution in the wake of the COVID-19 pandemic, beyond the scope of my functions in it as an MD.

The intended use of this platform is to provide local and regional health officials with easy-accessible and timely reports of critical information relevant to the management of COVID-19 patients and to rapidly deliver test results to patients and clinicians. The aforementioned reports are produced in several file formats, including PDF, Excel XLS and XLSX, HTML, and plain text.

Please note that I was not able to fully comply with coding best practices, including proper commenting and modularity, because in the wake of the COVID-19 pandemic, all these components had to be developed and regularly updated within very short timetables in order to satisfy constantly changing needs. Nevertheless, I tried, to the best of my ability, to keep the code readable, efficient, and maintainable.

This repository is currently archived.

## Components

This platform comprises three major components:

1. A relational database;
2. A small, lightweight website that is only accessible within the institution;
3. A set of console tools.

### Relational database

The relational database runs on MySql and contains tables for user accounts, login session management, patient and laboratory test data, management of secure test result delivery via email, phone, or printed format, and auditing of relevant database actions.

### Website

The mini-lightweight website was developed in PHP, targeting HTML 4.01 with CSS. It allows authenticated users to access and edit critical information relevant to the management of COVID-19 patients in a timely manner, including patient status, laboratory test results, and patient summary information. For each user, access privileges are tiered based on configurable profiles. HTML 4.01 was used for compatibility with older versions of Internet Explorer still in use within the institution.

### Console tools

The console tools were developed in Python and perform several functions.

The tool "update.py" is used to regularly update the database from multiple sources, including patient, sample, and laboratory test data exported from the Laboratory Information System (LIS) in use by the institution and patient data from XLSX files.

The tool "reports.py" is used to automatically generate different types of reports of patient data, laboratory test data, and stock information for relevant laboratory consumables in PDF, XLSX, XLS, HTML, and plain text formats and to also send these by email. One of those report types summarizes results for COVID-19 diagnostic tests combined with patient data, including name, date of birth, record and healthcare system IDs, address, and previous test results. This report also adds a cross-table with test counts classified by assay method and test result. Another report type divides patients by groups that are relevant to health officials in the field, for example, to highlight results for patients that belong to a specific institution. Another report type aggregates test counts by institution for social institutions of interest, also categorizing them by method and result.

The tool "transmission.py" is used to manage the delivery of test results via email, phone, and printed format. It implements a mechanism for protection of personal data integrity by which a patient's request for access to his/her/their laboratory test results is validated via unique and non-transmissible tokens. Within the use of this tool, users' actions are recorded in audit tables.

The module named "listools.py" consists mostly of a collection of methods to interface with, normalize, and convert data exported from the institutions' LIS and is also a common dependency of other tools that I have created.

## Operating system

These components target Windows XP and newer versions of Windows to ensure widespread compatibility across even the oldest computers in use within the institution.
