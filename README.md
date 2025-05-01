# Unicart

**Unicart** is a robust, object-oriented PHP shopping cart system designed for flexibility, modularity, and precision. It allows developers to manage individual items or full carts with support for detailed pricing operations such as discounts, taxes, and delivery charges at both item and cart levels as well as empowers developers to generate invoices based on the cart.

---

## Features

- Add, manage, and calculate totals for products
- Apply item-level and cart-level:
  - Discounts
  - Taxes
  - Delivery charges
- Built-in state enforcement to prevent pricing conflicts
- Fully encapsulated `Item` class for independent item operations
- Structured output formatting (array, object, JSON)
- Centralized validation logic using traits
- Extensible and well-documented object-oriented design
- Custom Exceptions: UnicartException and ItemException for precise error handling.
- Conditional Logic Support: when() and unless() methods for conditional operation execution using callbacks or boolean values.
- Discount Stacking Toggle: Enable or restrict stacking of discounts independently at both item and cart levels.
- Rounding Mode Configuration: Set custom rounding behavior on totals for both items and the cart.
- Item Override Control: Prevent or allow overriding items with the same ID.
- Meta Data Support: Attach developer-defined meta information for item.
- Locale system: Multi-language error handling
- Invoice generation: Styled HTML invoice with full cart summary, company details, and optional PDF download with RTL/LTR rendering.

---

## Documentation

For full documentation, usage and explanation, please visit https://adarsh-haldar.free.nf/unicart-docs

---

## Installation

To install Unicart, use Composer:

```sh
composer require adarshhaldar/unicart
```

---

## Usage
```php
use Unicart\Unicart;
                    
try {
    $cart = new Unicart();
                
    $cart->addItem('item_1', 200, 2);    // (id, price, quantity)
                
    $cart->applyFlatDiscountOnItem('item_1', 10);    // (id, discount)
    $cart->applyDeliveryChargeOnCart(50); // delivery charge
                
    $summary = $cart->toArray(); // Also available: toJson(), toObject()
    print_r($summary);
} catch (Exception $e) {
    echo $e->getMessage();
}
```

## Author
Developed by Adarsh Haldar

