(() => {
    // The blocks have no API to show custom content next to a shipping rate, so the breakdowns, rendered server-side
    // by Plugin::renderBlocksSolutions(), are injected into WC's own markup and re-injected whenever React redraws it.

    const targets = (cartBlock, checkoutBlock, pkgIdx, rateId) => {
        if (checkoutBlock) {
            const pkg = checkoutBlock.querySelectorAll('.wc-block-components-shipping-rates-control__package')[pkgIdx];
            const option = pkg?.querySelector(`input[value="${CSS.escape(rateId)}"]`)
                ?.closest('.wc-block-components-radio-control__option')
                ?.querySelector('.wc-block-components-radio-control__option-layout');
            return option ? [option, 'beforeend'] : null;
        }
        const via = cartBlock?.querySelector('.wc-block-components-totals-shipping__via');
        return via ? [via, 'afterend'] : null;
    };

    const sync = () => {
        const cart = wp.data.select('wc/store/cart').getCartData();
        const html = cart.extensions?.wbsng || {};
        const cartBlock = document.querySelector('.wp-block-woocommerce-cart');
        const checkoutBlock = document.querySelector('.wp-block-woocommerce-checkout');

        const wanted = new Set();
        cart.shippingRates.forEach((pkg, pkgIdx) => pkg.shipping_rates.forEach(rate => {
            const solution = html[pkg.package_id]?.[rate.rate_id];
            const content = solution?.[checkoutBlock ? 'checkout' : 'cart'] && solution.html;
            const target = rate.selected && content && targets(cartBlock, checkoutBlock, pkgIdx, rate.rate_id);
            if (!target) {
                return;
            }
            const [anchor, position] = target;
            const key = `${pkg.package_id}/${rate.rate_id}`;
            const existing = position === 'afterend' ? anchor.nextElementSibling : anchor.lastElementChild;
            let el = existing?.dataset.wbsngSolution === key ? existing : null;
            if (!el) {
                el = document.createElement('div');
                el.dataset.wbsngSolution = key;
                anchor.insertAdjacentElement(position, el);
            }
            // innerHTML is re-serialized by the browser, so compare against the source instead
            if (el.wbsngContent !== content) {
                el.wbsngContent = content;
                el.innerHTML = content;
            }
            wanted.add(el);
        }));

        document.querySelectorAll('[data-wbsng-solution]').forEach(el => wanted.has(el) || el.remove());
    };

    let scheduled = false;
    const schedule = () => {
        if (!scheduled) {
            scheduled = true;
            requestAnimationFrame(() => { scheduled = false; sync(); });
        }
    };

    wp.data.subscribe(schedule, 'wc/store/cart');
    new MutationObserver(schedule).observe(document.body, {childList: true, subtree: true});
})();