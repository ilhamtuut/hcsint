<!-- sidebar menu -->
<ul class="sidebar-menu" data-widget="tree">
    <li class="{{ isset($page) && $page == 'home' ? 'active' : '' }}"><a href="{{route('home')}}"><i class="icon_house_alt"></i> <span>Dashboard</span></a></li>
    {{-- <li class="{{ isset($page) && $page == 'av_coin' ? 'active' : '' }}"><a href="{{route('avcoin.index')}}"><i class="icon_lifesaver"></i> <span>AV</span></a></li> --}}
    {{-- <li class="{{ isset($page) && $page == 'av_explorer' ? 'active' : '' }}"><a href="{{route('avcoin.explorer')}}"><i class="icon_globe-2"></i> <span>HCS Techno</span></a></li> --}}
    {{-- <li class="{{ isset($page) && $page == 'marketplace' ? 'active' : '' }}"><a href="{{route('marketplace.index')}}"><i class="icon-basket"></i> <span>Market Place</span></a></li> --}}
    {{-- <li class="{{ isset($page) && $page == 'metatrader' ? 'active' : '' }}"><a href="{{route('metatrader.index')}}"><i class="icon_datareport"></i> <span>Metatrader</span></a></li> --}}
    <li class="{{ isset($page) && $page == 'portofolio' ? 'active' : '' }}"><a href="{{route('portofolio.index')}}"><i class="icon_document_alt"></i> <span>Portfolio</span></a></li>
    {{-- <li class="{{ isset($page) && $page == 'estimated_asset' ? 'active' : '' }}"><a href="{{route('asset.index')}}"><i class="icon_folder-open_alt"></i> <span>Estimated Asset</span></a></li> --}}
    {{-- <li class="treeview {{ isset($page) && $page == 'av_explorer' ? 'active' : '' }}">
        <a href="#"><i class="icon_globe-2"></i> <span>HCS Techno</span> <i class="fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
            <li class="{{ isset($active) && $active == 'explore' ? 'active' : '' }}"><a href="{{route('avcoin.explorer')}}">- HCS Explorer</a></li>
            <li class="{{ isset($active) && $active == 'address' ? 'active' : '' }}"><a href="{{(Auth::user()->wallet) ? route('avcoin.address',Auth::user()->wallet->address) : '#'}}">- HCS Key</a></li>
        </ul>
    </li> --}}
    @role(['member'])
        {{-- <li class="treeview {{ isset($page) && $page == 'product' ? 'active' : '' }}">
            <a href="#"><i class="icon_cart_alt"></i> <span>Product</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'create_product' ? 'active' : '' }}"><a href="{{ route('product.create') }}">- Add Product</a></li>
                <li class="{{ isset($active) && $active == 'list_product' ? 'active' : '' }}"><a href="{{ route('product.myProduct') }}">- My Product</a></li>
            </ul>
        </li> --}}
        <li class="treeview {{ isset($page) && $page == 'team' ? 'active' : '' }}">
            <a href="#"><i class="fa fa-sitemap"></i> <span>Team</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'sponsor' ? 'active' : '' }}"><a href="{{route('team.index')}}">- Sponsor Tree</a></li>
                <li class="{{ isset($active) && $active == 'network' ? 'active' : '' }}"><a href="{{route('team.network')}}">- Network Tree</a></li>
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'program' ? 'active' : '' }}">
            <a href="#"><i class="icon_archive_alt"></i> <span>Buy Package</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'index' ? 'active' : '' }}"><a href="{{route('program.index')}}">- Buy</a></li>
                <li class="{{ isset($active) && $active == 'history' ? 'active' : '' }}"><a href="{{route('program.history')}}">- History</a></li>
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'convert' ? 'active' : '' }}">
            <a href="#"><i class="fa fa-exchange"></i> <span>Convert</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'index' ? 'active' : '' }}"><a href="{{route('convert.index')}}">- Cash Wallet</a></li>
                {{-- <li class="{{ isset($active) && $active == 'voucher' ? 'active' : '' }}"><a href="{{route('convert.voucher')}}">- Voucher</a></li> --}}
                {{-- <li class="{{ isset($active) && $active == 'topup' ? 'active' : '' }}"><a href="{{route('convert.topup')}}">- Topupclingg</a></li> --}}
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'withdraw' ? 'active' : '' }}">
            <a href="#"><i class="icon_currency"></i> <span>Sell CW</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'index' ? 'active' : '' }}"><a href="{{route('withdraw.index')}}">- Bank</a></li>
                <li class="{{ isset($active) && $active == 'history' ? 'active' : '' }}"><a href="{{route('withdraw.history')}}">- History</a></li>
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'bonus' ? 'active' : '' }}">
            <a href="#"><i class="icon-layers"></i> <span>Platform Network Bonus</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'pasif' ? 'active' : '' }}"><a href="{{route('bonus.pasif')}}">- Share Profit Trade</a></li>
                <li class="{{ isset($active) && $active == 'sponsor' ? 'active' : '' }}"><a href="{{route('bonus.active','sponsor')}}">- Sponsor</a></li>
                <li class="{{ isset($active) && $active == 'pairing' ? 'active' : '' }}"><a href="{{route('bonus.active','pairing')}}">- Pairing</a></li>
            </ul>
        </li>
    @endrole

    @role(['metatrader'])
        <li class="treeview {{ isset($page) && $page == 'setting' ? 'active' : '' }}">
            <a href="#"><i class="icon-gears"></i> <span>Settings</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'metatrader' ? 'active' : '' }}"><a href="{{ route('metatrader.list') }}">- Meta Trader</a></li>
                <li class="{{ isset($active) && $active == 'video' ? 'active' : '' }}"><a href="{{ route('video.index') }}">- Asset Crypto</a></li>
            </ul>
        </li>
    @endrole

    @role(['admin','super_admin'])
        {{-- @role(['super_admin'])
            <li class="{{ isset($page) && $page == 'voucher' ? 'active' : '' }}"><a href="{{route('voucher.index')}}"><i class="icon_creditcard"></i> <span>Voucher</span></a></li>
        @endrole --}}

        <li class="treeview {{ isset($page) && $page == 'balance' ? 'active' : '' }}">
            <a href="#"><i class="icon_wallet"></i> <span>Balance</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                {{-- <li class="{{ isset($active) && $active == 'av' ? 'active' : '' }}"><a href="{{ route('avcoin.list') }}">- AV</a></li> --}}
                <li class="{{ isset($active) && $active == 'wallet' ? 'active' : '' }}"><a href="{{ route('balance.index') }}">- Wallet</a></li>
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'setting' ? 'active' : '' }}">
            <a href="#"><i class="icon-gears"></i> <span>Settings</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'bank' ? 'active' : '' }}"><a href="{{ route('setting.bank') }}">- Bank</a></li>
                {{-- <li class="{{ isset($active) && $active == 'ico' ? 'active' : '' }}"><a href="{{ route('setting.ico') }}">- Ico</a></li> --}}
                <li class="{{ isset($active) && $active == 'price' ? 'active' : '' }}"><a href="{{ route('setting.index') }}">- Price</a></li>
                <li class="{{ isset($active) && $active == 'package' ? 'active' : '' }}"><a href="{{ route('setting.package') }}">- Package</a></li>
                {{-- <li class="{{ isset($active) && $active == 'metatrader' ? 'active' : '' }}"><a href="{{ route('metatrader.list') }}">- Meta Trader</a></li> --}}
                {{-- <li class="{{ isset($active) && $active == 'video' ? 'active' : '' }}"><a href="{{ route('video.index') }}">- Asset Crypto</a></li> --}}
                <li class="{{ isset($active) && $active == 'question' ? 'active' : '' }}"><a href="{{ route('question.index') }}">- Question</a></li>
                <li class="{{ isset($active) && $active == 'composition' ? 'active' : '' }}"><a href="{{ route('setting.composition') }}">- Composition</a></li>
            </ul>
        </li>

        {{-- <li class="treeview {{ isset($page) && $page == 'product' ? 'active' : '' }}">
            <a href="#"><i class="icon_cart_alt"></i> <span>Product</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'category' ? 'active' : '' }}"><a href="{{ route('product.category.index') }}">- Category</a></li>
                <li class="{{ isset($active) && $active == 'create_product' ? 'active' : '' }}"><a href="{{ route('product.create') }}">- Add Product</a></li>
                <li class="{{ isset($active) && $active == 'list_product' ? 'active' : '' }}"><a href="{{ route('product.index') }}">- List Product</a></li>
            </ul>
        </li> --}}

        <li class="treeview {{ isset($page) && $page == 'user' ? 'active' : '' }}">
            <a href="#"><i class="fa fa-users"></i> <span>Users</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'create' ? 'active' : '' }}"><a href="{{route('user.index')}}">- Create User</a></li>
                @foreach (App\Models\Role::all() as $role)
                @if(Auth::user()->hasRole('admin') && $role->name == 'member')
                    <li class="{{ isset($active) && $active == $role->name ? 'active' : '' }}"><a href="{{ route('user.list',$role->name) }}">- List {{$role->display_name}}</a></li>
                @elseif(Auth::user()->hasRole('super_admin'))
                    <li class="{{ isset($active) && $active == $role->name ? 'active' : '' }}"><a href="{{ route('user.list',$role->name) }}">- List {{$role->display_name}}</a></li>
                @endif
                @endforeach
                <li class="{{ isset($active) && $active == 'list_sponsor' ? 'active' : '' }}"><a href="{{route('user.list_sponsor')}}">- List Sponsor</a></li>
                {{-- <li class="{{ isset($active) && $active == 'list_wallet' ? 'active' : '' }}"><a href="{{route('user.list_wallet')}}">- List Address AV</a></li> --}}
            </ul>
        </li>


        <li class="treeview {{ isset($page) && $page == 'convert' ? 'active' : '' }}">
            <a href="#"><i class="fa fa-exchange"></i> <span>Convert</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'list_convert' ? 'active' : '' }}"><a href="{{route('convert.list')}}">- Cash Wallet</a></li>
                {{-- <li class="{{ isset($active) && $active == 'list_convert_voucher' ? 'active' : '' }}"><a href="{{route('convert.list_voucher')}}">- Voucher</a></li> --}}
                {{-- <li class="{{ isset($active) && $active == 'list_topup' ? 'active' : '' }}"><a href="{{route('convert.list_topup')}}">- Topupclingg</a></li> --}}
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'withdraw' ? 'active' : '' }}">
            <a href="#"><i class="icon_currency"></i> <span>Sell CW</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'bank' ? 'active' : '' }}"><a href="{{route('withdraw.list','bank')}}">- Bank</a></li>
                {{-- <li class="{{ isset($active) && $active == 'usdt' ? 'active' : '' }}"><a href="{{route('withdraw.list','usdt')}}">- USDt</a></li> --}}
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'program' ? 'active' : '' }}">
            <a href="#"><i class="icon_archive_alt"></i> <span>Invest</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'by_admin' ? 'active' : '' }}"><a href="{{route('program.by_admin')}}">- Register Plan</a></li>
                <li class="{{ isset($active) && $active == 'list_package_admin' ? 'active' : '' }}"><a href="{{route('program.list','admin')}}">- List Plan by Admin</a></li>
                <li class="{{ isset($active) && $active == 'list_package_member' ? 'active' : '' }}"><a href="{{route('program.list','member')}}">- List Plan by Member</a></li>
                {{-- <li class="{{ isset($active) && $active == 'invest_avcoin' ? 'active' : '' }}"><a href="{{route('program.list_av')}}">- Register with AV</a></li> --}}
            </ul>
        </li>

        <li class="treeview {{ isset($page) && $page == 'bonus' ? 'active' : '' }}">
            <a href="#"><i class="fa fa-tasks"></i> <span>Platform Network Bonus</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'list_roi' ? 'active' : '' }}"><a href="{{route('bonus.list','roi')}}">- Share Profit Trade</a></li>
                <li class="{{ isset($active) && $active == 'list_sponsor' ? 'active' : '' }}"><a href="{{route('bonus.list','sponsor')}}">- Sponsor</a></li>
                <li class="{{ isset($active) && $active == 'list_pairing' ? 'active' : '' }}"><a href="{{route('bonus.list','pairing')}}">- Pairing</a></li>
                <li class="{{ isset($active) && $active == 'max_profit' ? 'active' : '' }}"><a href="{{route('bonus.max')}}">- Maximum Profit</a></li>
            </ul>
        </li>
        @role('admin')
            <li class="treeview {{ isset($page) && $page == 'team' ? 'active' : '' }}">
            <a href="#"><i class="fa fa-sitemap"></i> <span>Team</span> <i class="fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li class="{{ isset($active) && $active == 'sponsor' ? 'active' : '' }}"><a href="{{route('team.index')}}">- Sponsor Tree</a></li>
                <li class="{{ isset($active) && $active == 'network' ? 'active' : '' }}"><a href="{{route('team.network')}}">- Network Tree</a></li>
            </ul>
            </li>
        @endrole
    @endrole
</ul>
