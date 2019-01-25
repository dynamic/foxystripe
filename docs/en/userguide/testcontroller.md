##Testing order processing on local
Visiting `/foxytest` in dev mode will generate orders, order details, and members.
It will do this by creating a sample XML document and send it to the `/foxystripe` endpoint.

###Configuration

The data used in the test can be modified, from date to the password of the user.

```yml
Dynamic\FoxyStripe\Controller\DataTestController:
  data:
    TransactionDate: now
    OrderID: auto
    Email: auto
    Password: password
```

Setting `TransactionDate` to `now` will result in the `<transaction_date>` to be the current time.

Setting `OrderID` or `Email` to `auto` will cause an order id and new email to be generated for every order the endpoint creates.

`Password` is the unencrypted password for the user.
To use an encrypted password use `Salt`, `HashType`, and `HashedPassword`.

#### Order Details
The endpoint will generate an order detail if none is supplied.
