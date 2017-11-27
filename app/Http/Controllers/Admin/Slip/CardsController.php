<?php

namespace App\Http\Controllers\Admin\Slip;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Dictionary;
use App\Models\Receipt;
use App\Http\Requests\Admin\Slip\CardsCreateRequest;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use PDF;

class CardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->query->count() > 0) {
            $cards = Card::whereBetween('receipt_date', [
                $request->query->get('start_date'),
                $request->query->get('end_date')
            ])->withCount('receipts')->get();
        } else {
            $cards = Card::yetPrintedWithReceiptsCount()->get();
        }

        return view('admin.slip.cards.index')
            ->with(compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $card = new Card;
        $dictionaries = Dictionary::all();

        return view('admin.slip.cards.create')
            ->with(compact('card', 'dictionaries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CardsCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $card = new Card;
            $card->fill($request->all());
            $card->save();

            foreach ($request->receipts as $receipt) {
                if (is_null($receipt['payee'])) continue;

                $card->receipts()->save(new Receipt($receipt));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->route('admin.slip.cards.create')
                ->withInput()
                ->with('message_error', 'エラーが発生しました。入力内容をご確認下さい。');
        }

        return redirect(route('admin.slip.cards.index'))
            ->with('message_success', 'Successfully Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $card = Card::findOrFail($id);

        $card->delete();

        return $id;
    }

    public function pdf(Request $request)
    {
        $cards = Card::with(['receipts' => function ($query) use ($request) {
            $query->whereIn('card_id', $request->card_ids);
        }])->get();

        return PDF::loadView('admin.slip.cards.pdf', compact('cards'))->inline('slip.pdf');
    }

    public function printed(Request $request)
    {
        Card::whereIn('id', $request->card_ids)->update(['printed_at' => Carbon::now()]);

        return $request->card_ids;
    }
}
