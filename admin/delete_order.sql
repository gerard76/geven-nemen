-- xxx = order id, ook voor auto inc
DELETE FROM orders WHERE id = xxx
DELETE FROM orders_products WHERE orders_id=xxx
DELETE FROM orders_products_attributes WHERE orders_id=xxx
DELETE FROM orders_products_download WHERE orders_id=xxx
DELETE FROM orders_status_history WHERE orders_id=xxx
DELETE FROM orders_total WHERE orders_id=xxx
ALTER TABLE orders AUTO_INCREMENT = xxx
