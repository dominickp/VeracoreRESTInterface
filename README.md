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
