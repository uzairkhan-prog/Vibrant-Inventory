{{-- Shared JS for purchase return create/edit. Expects $excludeId and $selectedKey ("purchaseId:productId"). --}}
<script>
    $(document).ready(function() {

        const productsUrl = @json(route('purchase-returns.supplierProducts', ['supplier' => '__ID__']));
        const excludeId = @json($excludeId);
        let selectedKey = @json($selectedKey);

        $('.select2').select2({
            placeholder: "Search here...",
            allowClear: true
        });

        function updateSupplierBalance() {
            let balance = $('#supplierSelect').find(':selected').data('balance') || 0;
            $('#supplierBalance').text(parseFloat(balance).toFixed(2));
        }

        // Load the selected supplier's purchase lines
        function loadProducts() {
            let supplierId = $('#supplierSelect').val();
            if (!supplierId) {
                $('#productSelect').html('<option value="">-- Select Product --</option>').trigger('change');
                return;
            }

            let url = productsUrl.replace('__ID__', supplierId) + (excludeId ? '?exclude=' + excludeId : '');

            $.get(url, function(data) {
                let options = '<option value="">-- Select Product --</option>';
                data.forEach(function(p) {
                    // Fully returned lines are hidden, except the one being edited
                    if (p.returnable <= 0 && p.key !== selectedKey) return;

                    let label = `${p.name} (Purchase #${p.purchase_id}${p.date ? ' - ' + p.date : ''}) - Returnable: ${p.returnable}`;
                    options += `<option value="${p.key}"
                        data-product-id="${p.id}"
                        data-purchase-id="${p.purchase_id}"
                        data-returnable="${p.returnable}"
                        data-stock="${p.stock}"
                        data-price="${p.unit_cost}"
                        data-packing="${p.packing ?? ''}"
                        ${p.key === selectedKey ? 'selected' : ''}>${$('<div>').text(label).html()}</option>`;
                });
                $('#productSelect').html(options).trigger('change');
            });
        }

        // Fill product details; keepInputs = true on first load so saved/old values stay
        function updateProductDetails(keepInputs) {
            let selected = $('#productSelect').find(':selected');

            $('#productReturnable').text(selected.data('returnable') || 0);
            $('#productStock').text(selected.data('stock') || 0);
            $('#productPrice').text(parseFloat(selected.data('price') || 0).toFixed(2));
            $('#productPack').text(selected.data('packing') || '-');
            $('#purchaseIdInput').val(selected.data('purchase-id') || '');
            $('#productIdInput').val(selected.data('product-id') || '');

            if (!keepInputs) {
                $('#packingInput').val(selected.data('packing') || '');
                $('#qtyReturn').val('');
                $('#cutAmount').val(0);
            }

            $('#qtyReturn').attr('max', maxQty() || null);
        }

        // Can't exceed what's left on the invoice, or what's in stock
        function maxQty() {
            let selected = $('#productSelect').find(':selected');
            if (!selected.val()) return 0;
            return Math.min(parseInt(selected.data('returnable')) || 0, parseInt(selected.data('stock')) || 0);
        }

        // Auto-calculate amount (Total Value - Deduction Amount)
        function calculateAmountDeducted() {
            let qty = parseFloat($('#qtyReturn').val()) || 0;
            let price = parseFloat($('#productSelect').find(':selected').data('price') || 0);
            let totalValue = qty * price;
            let cut = parseFloat($('#cutAmount').val()) || 0;

            if (cut > totalValue) {
                cut = totalValue;
                $('#cutAmount').val(cut.toFixed(2));
            }

            let netCredit = totalValue - cut;

            $('#totalReturnValue').text(totalValue.toFixed(2));
            $('#summaryCutAmount').text(cut.toFixed(2));
            $('#summaryNetCredit').text(netCredit.toFixed(2));
            $('#amountDeducted').val(netCredit.toFixed(2));

            let max = maxQty();
            if ($('#productSelect').val() && qty > max) {
                $('#qtyWarning').text('Maximum returnable quantity is ' + max + '.').removeClass('d-none');
            } else {
                $('#qtyWarning').addClass('d-none');
            }
        }

        let firstLoad = true;

        $('#supplierSelect').on('change', function() {
            updateSupplierBalance();
            if (!firstLoad) selectedKey = null;
            loadProducts();
        });

        $('#productSelect').on('change', function() {
            updateProductDetails(firstLoad);
            firstLoad = false;
            calculateAmountDeducted();
        });

        $('#qtyReturn').on('input', calculateAmountDeducted);
        $('#cutAmount').on('input', calculateAmountDeducted);

        // Initial load (edit page / validation error)
        updateSupplierBalance();
        if ($('#supplierSelect').val()) {
            loadProducts();
        } else {
            firstLoad = false;
        }
    });
</script>
