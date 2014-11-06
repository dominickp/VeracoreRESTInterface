VeracoreApi
===========
The plan is to develop a REST API built on Silex as a wrapper for Veracore's SOAP API. All methods require *application/json* as the request content type.

## Methods
###AddOrder
Submits an order into Veracore. See the [AddOrder example JSON](https://github.com/dominickp/VeracoreREST/blob/master/example/AddOrder.json).

Route: ```POST /order```

###GetOrderInfo
Gets information related to a particular Veracore order.

Route: ```GET /order/{order_id}```

###GetOffer
Gets information related to a particular Veracore offer. Additional GET parameters are *id* and *name* which dictate if the *search_string* should be used to search for the Offer ID, Offer Name, or both. Booleans are expected and if both are left blank, it will default to both true. Is possible to return more than one result if the query is vague enough.

Route: ```GET /offer/{search_string}?id=true&name=false```

###GetOffers
Returns offer information for any offers within a category access group. You can further specify a search term but that is optional. It also has the same GET parameters from GetOffer.

Route: ```GET /offers/{category_access_group}/{search_string}?id=true&name=false```

Example: ```GET /offers/salesforce```
