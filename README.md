# Number-Analyzer
Sorts mobile numbers by header information into operators


*UPDATE:*
Currently in the process of porting this project to a nodejs and mongodb base. Will now also include a simple web UI to allow:
- use a set of APIs to achieve functions.
- uploading CSV files containing MSISDNs and outputting a processed CSV file containing the categorized numbers.
- entering a single number and getting the corresponding operator.
- may support inserting an operator specific API path to which a MSISDN or file can be submitted to the result of numbr analysis is returned and provided back to user. 
 - system will include a madatory field for specifying what operator owns the specified API.

*NOTE that application by default based on a simple number header analysis and does not account for ported numbers*
