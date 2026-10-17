<?php

require_once 'config/database.php';
require_once 'config/helpers.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/*
|--------------------------------------------------------------------------
| Cart Actions
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // ADD PRODUCT
    if ($action === 'add') {

        $id = (int) $_POST['product_id'];
        $qty = max(1, (int) $_POST['quantity']);

        $st = $pdo->prepare(
            "SELECT stock FROM products WHERE id=? AND status=1"
        );

        $st->execute([$id]);
        $p = $st->fetch();

        if ($p) {
            $_SESSION['cart'][$id] = min(
                ($_SESSION['cart'][$id] ?? 0) + $qty,
                (int) $p['stock']
            );

            flash('success', 'Product added to cart.');
        }

        redirect('cart.php');
    }

    // UPDATE CART / CHECKOUT
    // Both buttons submit the same quantity fields, so a quantity the user
    // edited but never explicitly "Updated" is no longer silently lost when
    // they click straight through to Checkout.
    if ($action === 'update' || $action === 'checkout') {

        foreach ($_POST['qty'] ?? [] as $id => $qty) {

            $id = (int) $id;
            $qty = (int) $qty;

            $st = $pdo->prepare(
                "SELECT stock FROM products WHERE id=?"
            );

            $st->execute([$id]);
            $p = $st->fetch();

            if (!$p || $qty <= 0) {

                unset($_SESSION['cart'][$id]);

            } else {

                $_SESSION['cart'][$id] = min(
                    $qty,
                    (int) $p['stock']
                );
            }
        }

        if ($action === 'checkout') {
            redirect('checkout.php');
        }

        flash('success', 'Cart updated.');
        redirect('cart.php');
    }

    redirect('cart.php');
}


/*
|--------------------------------------------------------------------------
| Remove Product From Cart
|--------------------------------------------------------------------------
*/

if (isset($_GET['remove'])) {

    $removeId = (int) $_GET['remove'];

    if (isset($_SESSION['cart'][$removeId])) {

        unset($_SESSION['cart'][$removeId]);

        flash('success', 'Product removed from cart.');
    }

    redirect('cart.php');
}


/*
|--------------------------------------------------------------------------
| Get Cart Products
|--------------------------------------------------------------------------
*/

$items = [];
$total = 0;

if ($_SESSION['cart']) {

    $ids = array_keys($_SESSION['cart']);

    $ph = implode(',', array_fill(0, count($ids), '?'));

    $st = $pdo->prepare(
        "SELECT * FROM products WHERE id IN ($ph)"
    );

    $st->execute($ids);

    foreach ($st as $p) {

        $qty = $_SESSION['cart'][$p['id']];

        $sub = $qty * $p['price'];

        $total += $sub;

        $items[] = [
            'p'   => $p,
            'qty' => $qty,
            'sub' => $sub
        ];
    }
}

$pageTitle = 'Shopping Cart - MobiXa';

include 'includes/header.php';

?>

<section class="section">

    <div class="container">

        <h1>Shopping Cart</h1>

        <?php if ($msg = flash('success')): ?>

            <div class="alert success">
                <?= e($msg) ?>
            </div>

        <?php endif; ?>


        <?php if (!$items): ?>

            <div class="empty">

                <h2>Your cart is empty</h2>

                <a class="btn" href="products.php">
                    Shop Now
                </a>

            </div>

        <?php else: ?>


            <form method="post" id="cart-form">

                <div class="table-wrap">

                    <table class="table">

                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>


                        <?php foreach ($items as $it): ?>

                            <?php $p = $it['p']; ?>

                            <tr data-row-id="<?= $p['id'] ?>">

                                <!-- PRODUCT -->

                                <td>
                                    <?= e($p['name']) ?>
                                </td>


                                <!-- PRICE -->

                                <td>
                                    <?= money($p['price']) ?>
                                </td>


                                <!-- QUANTITY -->

                                <td>

                                    <input
                                        class="qty"
                                        type="number"
                                        min="1"
                                        max="<?= $p['stock'] ?>"
                                        name="qty[<?= $p['id'] ?>]"
                                        value="<?= $it['qty'] ?>"
                                        data-id="<?= $p['id'] ?>"
                                        data-price="<?= $p['price'] ?>"
                                    >

                                </td>


                                <!-- SUBTOTAL -->

                                <td class="subtotal-cell" id="subtotal-<?= $p['id'] ?>" data-id="<?= $p['id'] ?>">
                                    <?= money($it['sub']) ?>
                                </td>


                                <!-- REMOVE -->

                                <td>

                                    <a
                                        href="cart.php?remove=<?= $p['id'] ?>"
                                        class="remove-btn"
                                        data-confirm="Are you sure you want to remove this product from your cart?"
                                    >
                                        Remove
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </table>

                </div>


                <!-- CART TOTAL -->

                <div class="cart-total">

                    <h2>
                        Total: <span id="cart-total-value"><?= money($total) ?></span>
                    </h2>


                    <div class="cart-actions">

                        <button
                            class="btn secondary"
                            type="submit"
                            name="action"
                            value="update"
                        >
                            Update Cart
                        </button>


                        <button
                            class="btn"
                            type="submit"
                            name="action"
                            value="checkout"
                        >
                            Checkout →
                        </button>

                    </div>

                </div>

            </form>


        <?php endif; ?>

    </div>

</section>

<script src="assets/js/cart-live.js"></script>

<?php include 'includes/footer.php'; ?>