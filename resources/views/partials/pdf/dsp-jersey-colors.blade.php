<div class="side-box">

    <div class="side-title">
        {{ $title ?? 'WARNA JERSEY' }}
    </div>

    <div class="side-content">

        <table width="100%" cellspacing="0" cellpadding="5">

            <tr>
                <td class="bold text-center gray">
                    BAJU
                </td>
            </tr>

            <tr>
                <td class="text-center upper">
                    {{ $shirtColor ?: '-' }}
                </td>
            </tr>

            <tr>
                <td class="bold text-center gray">
                    CELANA
                </td>
            </tr>

            <tr>
                <td class="text-center upper">
                    {{ $pantsColor ?: '-' }}
                </td>
            </tr>

            <tr>
                <td class="bold text-center gray">
                    KAOS KAKI
                </td>
            </tr>

            <tr>
                <td class="text-center upper">
                    {{ $socksColor ?: '-' }}
                </td>
            </tr>

        </table>

    </div>

</div>  