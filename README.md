# db
laravel package - DB functionality

# sample usage
```
use Nunahsan\Db\Paginator;

class yourClass {
  $select = \DB::table('tableName');
  $res = Paginator::get($select, 15);
  
  return response()->json([
      'data' => $res ?? []
  ]);
}
```
